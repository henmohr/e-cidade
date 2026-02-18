<?php

namespace App\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\RestosAPagarRepository;
use App\Repositories\Financeiro\Tesouraria\RestosAPagarRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class RestosAPagarService
{
    private RestosAPagarRepositoryInterface $repository;
    private LoggerInterface $logger;

    public function __construct(
        ?RestosAPagarRepositoryInterface $repository = null,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository ?? new RestosAPagarRepository();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, float|int>
     */
    public function obterResumo(?int $ano = null): array
    {
        $totais = $this->repository->obterTotais($ano);

        $resumo = [
            'ano' => $ano ?? (int) date('Y'),
            'restos_processados' => (float) $totais['restos_processados'],
            'restos_nao_processados' => (float) $totais['restos_nao_processados'],
            'restos_total' => (float) $totais['restos_processados'] + (float) $totais['restos_nao_processados'],
        ];

        $this->logger->info('Resumo de restos a pagar calculado.', $resumo);

        return $resumo;
    }
}

