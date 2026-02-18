<?php

namespace App\Repositories\Financeiro\Tesouraria;

interface FluxoCaixaRepositoryInterface
{
    public function obterSaldoAtual(int $conta, int $reduz, ?string $dataReferencia = null): ?float;

    public function obterEntradasPrevistasMes(int $mes): float;

    public function obterSaidasPrevistasMes(int $mes): float;
}

