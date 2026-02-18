<?php

namespace App\Services\Financeiro\ExecucaoOrcamentaria;

use App\Repositories\Financeiro\ExecucaoOrcamentaria\CicloDespesaRepository;
use App\Repositories\Financeiro\ExecucaoOrcamentaria\CicloDespesaRepositoryInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class CicloDespesaService
{
    private CicloDespesaRepositoryInterface $repository;
    private LoggerInterface $logger;

    public function __construct(
        ?CicloDespesaRepositoryInterface $repository = null,
        ?LoggerInterface $logger = null
    )
    {
        $this->repository = $repository ?? new CicloDespesaRepository();
        $this->logger = $logger ?? new NullLogger();
    }

    public function assertPodeEmpenhar(int $ano, int $codigoDotacao, ?float $valorEmpenho = null): void
    {
        if (!$this->repository->hasFixacao($ano, $codigoDotacao)) {
            $this->audit('empenho', false, [
                'ano' => $ano,
                'dotacao' => $codigoDotacao,
            ], 'Nao e permitido empenhar sem fixacao valida da dotacao informada.');
            throw new LogicException('Nao e permitido empenhar sem fixacao valida da dotacao informada.');
        }

        if ($valorEmpenho !== null && $valorEmpenho > 0) {
            $saldoDisponivel = $this->repository->getSaldoDisponivelDotacao($ano, $codigoDotacao);

            if ($saldoDisponivel === null) {
                $this->audit('empenho', false, [
                    'ano' => $ano,
                    'dotacao' => $codigoDotacao,
                    'valor_empenho' => $valorEmpenho,
                ], 'Nao foi possivel apurar saldo disponivel da dotacao para o empenho.');
                throw new LogicException('Nao foi possivel apurar saldo disponivel da dotacao para o empenho.');
            }

            if ($saldoDisponivel < $valorEmpenho) {
                $this->audit('empenho', false, [
                    'ano' => $ano,
                    'dotacao' => $codigoDotacao,
                    'valor_empenho' => $valorEmpenho,
                    'saldo_disponivel' => $saldoDisponivel,
                ], 'Nao e permitido empenhar valor acima do saldo disponivel da dotacao.');
                throw new LogicException('Nao e permitido empenhar valor acima do saldo disponivel da dotacao.');
            }
        }

        $this->audit('empenho', true, [
            'ano' => $ano,
            'dotacao' => $codigoDotacao,
            'valor_empenho' => $valorEmpenho,
        ]);
    }

    public function assertPodeLiquidar(int $numeroEmpenho): void
    {
        if (!$this->repository->hasEmpenho($numeroEmpenho)) {
            $this->audit('liquidacao', false, [
                'empenho' => $numeroEmpenho,
            ], 'Nao e permitido liquidar sem empenho correspondente.');
            throw new LogicException('Nao e permitido liquidar sem empenho correspondente.');
        }

        if (!$this->repository->hasFixacaoParaEmpenho($numeroEmpenho)) {
            $this->audit('liquidacao', false, [
                'empenho' => $numeroEmpenho,
            ], 'Nao e permitido liquidar sem fixacao valida da dotacao do empenho.');
            throw new LogicException('Nao e permitido liquidar sem fixacao valida da dotacao do empenho.');
        }

        $this->audit('liquidacao', true, [
            'empenho' => $numeroEmpenho,
        ]);
    }

    public function assertPodePagar(int $numeroEmpenho): void
    {
        if (!$this->repository->hasEmpenho($numeroEmpenho)) {
            $this->audit('pagamento', false, [
                'empenho' => $numeroEmpenho,
            ], 'Nao e permitido pagar sem empenho correspondente.');
            throw new LogicException('Nao e permitido pagar sem empenho correspondente.');
        }

        if (!$this->repository->hasFixacaoParaEmpenho($numeroEmpenho)) {
            $this->audit('pagamento', false, [
                'empenho' => $numeroEmpenho,
            ], 'Nao e permitido pagar sem fixacao valida da dotacao do empenho.');
            throw new LogicException('Nao e permitido pagar sem fixacao valida da dotacao do empenho.');
        }

        if (!$this->repository->hasLiquidacao($numeroEmpenho)) {
            $this->audit('pagamento', false, [
                'empenho' => $numeroEmpenho,
            ], 'Nao e permitido pagar sem liquidacao valida.');
            throw new LogicException('Nao e permitido pagar sem liquidacao valida.');
        }

        $this->audit('pagamento', true, [
            'empenho' => $numeroEmpenho,
        ]);
    }

    public function assertPodeRegistrarPagamentoPorOrdem(int $codigoOrdem): void
    {
        $numeroEmpenho = $this->repository->getNumeroEmpenhoPorOrdemPagamento($codigoOrdem);

        if (empty($numeroEmpenho)) {
            throw new LogicException('Nao foi possivel identificar o empenho da ordem de pagamento informada.');
        }

        $this->assertPodePagar((int) $numeroEmpenho);
    }

    public function assertSequenciaObrigatoria(int $numeroEmpenho): void
    {
        $hasEmpenho = $this->repository->hasEmpenho($numeroEmpenho);
        $hasFixacao = $hasEmpenho && $this->repository->hasFixacaoParaEmpenho($numeroEmpenho);
        $hasLiquidacao = $hasEmpenho && $this->repository->hasLiquidacao($numeroEmpenho);
        $hasPagamento = $hasEmpenho && $this->repository->hasPagamento($numeroEmpenho);

        if ($hasEmpenho && !$hasFixacao) {
            $mensagem = 'Sequencia invalida: existe empenho sem fixacao valida.';
            $this->audit('sequencia', false, ['empenho' => $numeroEmpenho], $mensagem);
            throw new LogicException($mensagem);
        }

        if ($hasPagamento && !$hasLiquidacao) {
            $mensagem = 'Sequencia invalida: existe pagamento sem liquidacao valida.';
            $this->audit('sequencia', false, ['empenho' => $numeroEmpenho], $mensagem);
            throw new LogicException($mensagem);
        }

        $this->audit('sequencia', true, [
            'empenho' => $numeroEmpenho,
            'fixacao' => $hasFixacao,
            'liquidacao' => $hasLiquidacao,
            'pagamento' => $hasPagamento,
        ]);
    }

    /**
     * @return array<string, bool>
     */
    public function obterSituacao(int $numeroEmpenho): array
    {
        $hasEmpenho = $this->repository->hasEmpenho($numeroEmpenho);
        $hasLiquidacao = $hasEmpenho && $this->repository->hasLiquidacao($numeroEmpenho);
        $hasPagamento = $hasLiquidacao && $this->repository->hasPagamento($numeroEmpenho);

        return [
            'empenho' => $hasEmpenho,
            'liquidacao' => $hasLiquidacao,
            'pagamento' => $hasPagamento,
        ];
    }

    /**
     * @param array<string, mixed> $contexto
     */
    private function audit(string $etapa, bool $permitido, array $contexto = [], string $motivo = ''): void
    {
        $payload = [
            'evento' => 'execucao_orcamentaria.ciclo_despesa',
            'etapa' => $etapa,
            'permitido' => $permitido,
            'motivo' => $motivo,
            'contexto' => $contexto,
        ];

        if ($permitido) {
            $this->logger->info('Validacao de ciclo da despesa executada.', $payload);
            return;
        }

        $this->logger->warning('Validacao de ciclo da despesa bloqueada.', $payload);
    }
}
