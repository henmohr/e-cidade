<?php

namespace App\Repositories\Financeiro\Tesouraria;

interface RestosAPagarRepositoryInterface
{
    /**
     * @return array<string, float>
     */
    public function obterTotais(?int $ano = null): array;
}

