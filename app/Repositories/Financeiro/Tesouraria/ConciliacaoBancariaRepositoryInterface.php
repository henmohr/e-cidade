<?php

namespace App\Repositories\Financeiro\Tesouraria;

interface ConciliacaoBancariaRepositoryInterface
{
    public function obterSaldoSistema(int $conta, int $reduz, ?string $dataReferencia = null): ?float;
}

