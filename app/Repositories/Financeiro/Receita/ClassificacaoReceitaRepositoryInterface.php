<?php

namespace App\Repositories\Financeiro\Receita;

interface ClassificacaoReceitaRepositoryInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function obterReceitaPorCodigo(int $codigoReceita): ?array;
}

