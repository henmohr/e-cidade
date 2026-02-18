<?php

namespace App\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\ConciliacaoBancariaRepository;
use App\Repositories\Financeiro\Tesouraria\ConciliacaoBancariaRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ConciliacaoBancariaService
{
    private ConciliacaoBancariaRepositoryInterface $repository;
    private LoggerInterface $logger;

    public function __construct(
        ?ConciliacaoBancariaRepositoryInterface $repository = null,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository ?? new ConciliacaoBancariaRepository();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, float|string|bool|null>
     */
    public function conciliar(
        int $conta,
        int $reduz,
        float $saldoExtrato,
        ?string $dataReferencia = null,
        float $tolerancia = 0.01
    ): array {
        $saldoSistema = $this->repository->obterSaldoSistema($conta, $reduz, $dataReferencia);

        if ($saldoSistema === null) {
            $resultado = [
                'status' => 'PENDENTE_SEM_SALDO_SISTEMA',
                'conta' => $conta,
                'reduz' => $reduz,
                'data_referencia' => $dataReferencia,
                'saldo_sistema' => null,
                'saldo_extrato' => $saldoExtrato,
                'diferenca' => null,
                'conciliado' => false,
            ];

            $this->logger->warning('Conciliacao bancaria sem saldo interno encontrado.', $resultado);
            return $resultado;
        }

        $diferenca = round($saldoExtrato - $saldoSistema, 2);
        $conciliado = abs($diferenca) <= $tolerancia;

        $resultado = [
            'status' => $conciliado ? 'CONCILIADO' : 'PENDENTE_DIVERGENCIA',
            'conta' => $conta,
            'reduz' => $reduz,
            'data_referencia' => $dataReferencia,
            'saldo_sistema' => $saldoSistema,
            'saldo_extrato' => $saldoExtrato,
            'diferenca' => $diferenca,
            'conciliado' => $conciliado,
        ];

        if ($conciliado) {
            $this->logger->info('Conciliacao bancaria concluida sem divergencia.', $resultado);
            return $resultado;
        }

        $this->logger->warning('Conciliacao bancaria com divergencia pendente.', $resultado);
        return $resultado;
    }
}

