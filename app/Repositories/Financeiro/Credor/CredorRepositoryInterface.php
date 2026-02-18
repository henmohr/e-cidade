<?php

namespace App\Repositories\Financeiro\Credor;

interface CredorRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function obterCredorPorCgm(int $numcgm): ?array;
}

