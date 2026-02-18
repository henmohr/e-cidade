<?php

namespace App\Repositories\Financeiro\Receita;

interface ControleReceitasRepositoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function obterReceitasConsolidadas(?string $dataInicial = null, ?string $dataFinal = null): array;
}

