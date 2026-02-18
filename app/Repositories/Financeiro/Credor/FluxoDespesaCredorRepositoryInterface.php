<?php

namespace App\Repositories\Financeiro\Credor;

interface FluxoDespesaCredorRepositoryInterface
{
    public function hasEmpenhoDoCredor(int $numeroEmpenho, int $numcgm): bool;

    public function hasAtestoDoEmpenho(int $numeroEmpenho): bool;

    public function hasPagamentoDoEmpenho(int $numeroEmpenho): bool;
}

