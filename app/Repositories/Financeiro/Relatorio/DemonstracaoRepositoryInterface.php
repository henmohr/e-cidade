<?php

namespace App\Repositories\Financeiro\Relatorio;

interface DemonstracaoRepositoryInterface
{
    /**
     * @return array<string, float>
     */
    public function obterDadosDvp(?string $dataInicial = null, ?string $dataFinal = null): array;

    /**
     * @return array<string, float>
     */
    public function obterDadosDfc(?string $dataInicial = null, ?string $dataFinal = null): array;
}
