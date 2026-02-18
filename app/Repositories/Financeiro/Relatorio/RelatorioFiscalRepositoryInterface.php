<?php

namespace App\Repositories\Financeiro\Relatorio;

interface RelatorioFiscalRepositoryInterface
{
    /**
     * @return array<string, float>
     */
    public function obterDadosRgf(?string $dataInicial = null, ?string $dataFinal = null): array;

    /**
     * @return array<string, float>
     */
    public function obterDadosRreo(?string $dataInicial = null, ?string $dataFinal = null): array;
}
