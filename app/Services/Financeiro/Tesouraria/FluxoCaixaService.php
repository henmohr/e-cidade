<?php

namespace App\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\FluxoCaixaRepository;
use App\Repositories\Financeiro\Tesouraria\FluxoCaixaRepositoryInterface;
use DateTimeImmutable;
use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class FluxoCaixaService
{
    private FluxoCaixaRepositoryInterface $repository;
    private LoggerInterface $logger;

    public function __construct(
        ?FluxoCaixaRepositoryInterface $repository = null,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository ?? new FluxoCaixaRepository();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, mixed>
     */
    public function projetar7Dias(
        int $conta,
        int $reduz,
        ?string $dataBase = null
    ): array {
        $base = $dataBase ? new DateTimeImmutable($dataBase) : new DateTimeImmutable('today');
        $saldoAtual = $this->repository->obterSaldoAtual($conta, $reduz, $base->format('Y-m-d'));

        if ($saldoAtual === null) {
            throw new LogicException('Nao foi possivel gerar previsao: saldo atual da conta nao encontrado.');
        }

        $linhas = [];
        $saldo = $saldoAtual;
        $menorSaldo = $saldoAtual;

        for ($i = 0; $i < 7; $i++) {
            $data = $base->modify("+{$i} day");
            $mes = (int) $data->format('n');
            $diasNoMes = (int) $data->format('t');

            $entradaDiaria = round($this->repository->obterEntradasPrevistasMes($mes) / max($diasNoMes, 1), 2);
            $saidaDiaria = round($this->repository->obterSaidasPrevistasMes($mes) / max($diasNoMes, 1), 2);

            $saldoInicialDia = $saldo;
            $saldo = round($saldo + $entradaDiaria - $saidaDiaria, 2);
            $menorSaldo = min($menorSaldo, $saldo);

            $linhas[] = [
                'data' => $data->format('Y-m-d'),
                'saldo_inicial' => $saldoInicialDia,
                'entradas_previstas' => $entradaDiaria,
                'saidas_previstas' => $saidaDiaria,
                'saldo_final' => $saldo,
            ];
        }

        $resultado = [
            'conta' => $conta,
            'reduz' => $reduz,
            'data_base' => $base->format('Y-m-d'),
            'saldo_inicial' => $saldoAtual,
            'saldo_final_previsto' => $saldo,
            'menor_saldo_previsto' => $menorSaldo,
            'dias' => $linhas,
        ];

        $this->logger->info('Previsao de fluxo de caixa em 7 dias gerada.', $resultado);
        return $resultado;
    }

    public function assertPodeProgramarPagamento(
        int $conta,
        int $reduz,
        float $valorProgramado,
        string $dataProgramada,
        ?string $dataBase = null
    ): void {
        if ($valorProgramado <= 0) {
            throw new LogicException('Valor programado deve ser maior que zero.');
        }

        $projecao = $this->projetar7Dias($conta, $reduz, $dataBase);
        $saldoPrevisto = $this->obterSaldoPrevistoNaData($projecao, $dataProgramada);

        if ($saldoPrevisto === null) {
            throw new LogicException('Data programada fora da janela de previsao de 7 dias.');
        }

        if ($saldoPrevisto < $valorProgramado) {
            $contexto = [
                'conta' => $conta,
                'reduz' => $reduz,
                'data_programada' => $dataProgramada,
                'saldo_previsto' => $saldoPrevisto,
                'valor_programado' => $valorProgramado,
            ];

            $this->logger->warning('Programacao financeira bloqueada por insuficiencia de caixa projetada.', $contexto);
            throw new LogicException('Programacao financeira bloqueada: saldo projetado insuficiente para o pagamento.');
        }

        $this->logger->info('Programacao financeira validada com saldo projetado suficiente.', [
            'conta' => $conta,
            'reduz' => $reduz,
            'data_programada' => $dataProgramada,
            'valor_programado' => $valorProgramado,
            'saldo_previsto' => $saldoPrevisto,
        ]);
    }

    /**
     * @param array<string, mixed> $projecao
     */
    private function obterSaldoPrevistoNaData(array $projecao, string $dataProgramada): ?float
    {
        foreach ($projecao['dias'] as $dia) {
            if (($dia['data'] ?? '') === $dataProgramada) {
                return (float) $dia['saldo_final'];
            }
        }

        return null;
    }
}

