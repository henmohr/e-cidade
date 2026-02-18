<?php

namespace App\Services\Financeiro\Integracao;

use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepository;
use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepositoryInterface;
use InvalidArgumentException;

class IntegracaoGovernamentalStatusService
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_ENVIADO = 'enviado';
    public const STATUS_ACEITO = 'aceito';
    public const STATUS_REJEITADO = 'rejeitado';

    private IntegracaoGovernamentalRepositoryInterface $repository;

    public function __construct(?IntegracaoGovernamentalRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new IntegracaoGovernamentalRepository();
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function registrarPendencia(string $sistema, string $referencia, array $payload = []): array
    {
        $codigo = $this->repository->criarRegistro($sistema, $referencia, self::STATUS_PENDENTE, $payload);

        return [
            'codigo' => $codigo,
            'sistema' => $sistema,
            'referencia' => $referencia,
            'status' => self::STATUS_PENDENTE,
        ];
    }

    public function atualizarStatus(
        int $codigo,
        string $statusAtual,
        string $novoStatus,
        ?string $protocoloExterno = null,
        ?string $mensagem = null
    ): void {
        $this->assertTransicaoValida($statusAtual, $novoStatus);
        $this->repository->atualizarRegistro($codigo, $novoStatus, $protocoloExterno, $mensagem);
    }

    /**
     * @return array<string, mixed>
     */
    public function monitorarFalhas(?string $sistema = null, int $limite = 100): array
    {
        $falhas = $this->repository->buscarPorStatus([self::STATUS_REJEITADO], $sistema, $limite);

        return [
            'sistema' => $sistema,
            'total_falhas' => count($falhas),
            'codigos' => array_values(array_map(static fn (array $falha): int => (int) ($falha['codigo'] ?? 0), $falhas)),
            'falhas' => $falhas,
        ];
    }

    /**
     * @return array<string, int|string|null>
     */
    public function reprocessarFalhas(?string $sistema = null, int $limite = 50): array
    {
        $falhas = $this->repository->buscarPorStatus([self::STATUS_REJEITADO], $sistema, $limite);
        $reprocessados = 0;

        foreach ($falhas as $falha) {
            $codigo = (int) ($falha['codigo'] ?? 0);

            if ($codigo <= 0) {
                continue;
            }

            $this->repository->incrementarTentativaReprocessamento($codigo);
            $this->repository->atualizarRegistro(
                $codigo,
                self::STATUS_PENDENTE,
                null,
                'Registro marcado para reprocessamento automatico'
            );
            $reprocessados++;
        }

        return [
            'sistema' => $sistema,
            'total_identificados' => count($falhas),
            'total_reprocessados' => $reprocessados,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function registrarResultadoHomologacao(
        int $codigo,
        string $resultado,
        string $protocoloExterno,
        ?string $mensagem = null
    ): array {
        $registro = $this->repository->buscarPorCodigo($codigo);

        if ($registro === null) {
            throw new InvalidArgumentException('Registro de integracao nao encontrado: ' . $codigo);
        }

        $statusAtual = (string) ($registro['status'] ?? self::STATUS_PENDENTE);
        $resultadoNormalizado = strtolower(trim($resultado));

        if (!in_array($resultadoNormalizado, [self::STATUS_ENVIADO, self::STATUS_ACEITO, self::STATUS_REJEITADO], true)) {
            throw new InvalidArgumentException('Resultado de homologacao invalido: ' . $resultado);
        }

        $this->atualizarStatus(
            $codigo,
            $statusAtual,
            $resultadoNormalizado,
            $protocoloExterno,
            $mensagem
        );

        return [
            'codigo' => $codigo,
            'sistema' => (string) ($registro['sistema'] ?? ''),
            'status_anterior' => $statusAtual,
            'status_novo' => $resultadoNormalizado,
            'protocolo_externo' => $protocoloExterno,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function gerarResumoHomologacao(?string $sistema = null, int $limite = 200): array
    {
        $pendentes = $this->repository->buscarPorStatus([self::STATUS_PENDENTE], $sistema, $limite);
        $enviados = $this->repository->buscarPorStatus([self::STATUS_ENVIADO], $sistema, $limite);
        $aceitos = $this->repository->buscarPorStatus([self::STATUS_ACEITO], $sistema, $limite);
        $rejeitados = $this->repository->buscarPorStatus([self::STATUS_REJEITADO], $sistema, $limite);

        return [
            'sistema' => $sistema,
            'totais' => [
                'pendente' => count($pendentes),
                'enviado' => count($enviados),
                'aceito' => count($aceitos),
                'rejeitado' => count($rejeitados),
            ],
            'pendencias' => [
                'pendente' => $pendentes,
                'enviado' => $enviados,
                'rejeitado' => $rejeitados,
            ],
        ];
    }

    private function assertTransicaoValida(string $statusAtual, string $novoStatus): void
    {
        $transicoes = [
            self::STATUS_PENDENTE => [self::STATUS_ENVIADO, self::STATUS_REJEITADO],
            self::STATUS_ENVIADO => [self::STATUS_ACEITO, self::STATUS_REJEITADO],
            self::STATUS_REJEITADO => [self::STATUS_PENDENTE, self::STATUS_ENVIADO],
            self::STATUS_ACEITO => [],
        ];

        $permitidos = $transicoes[$statusAtual] ?? [];

        if (!in_array($novoStatus, $permitidos, true)) {
            throw new InvalidArgumentException(sprintf(
                'Transicao de status invalida: %s -> %s',
                $statusAtual,
                $novoStatus
            ));
        }
    }
}
