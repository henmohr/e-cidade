<?php

namespace App\Repositories\Financeiro\Relatorio;

interface BalancoRepositoryInterface
{
    /**
     * @return array<string, float>
     */
    public function obterDadosBalancoPatrimonial(?string $dataInicial = null, ?string $dataFinal = null): array;

    /**
     * @return array<string, float>
     */
    public function obterDadosBalancoOrcamentario(?string $dataInicial = null, ?string $dataFinal = null): array;

    /**
     * @return array<string, float>
     */
    public function obterDadosBalancoFinanceiro(?string $dataInicial = null, ?string $dataFinal = null): array;
}
