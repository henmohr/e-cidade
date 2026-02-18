<?php

namespace App\Repositories\Financeiro\Tesouraria;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ConciliacaoBancariaRepository implements ConciliacaoBancariaRepositoryInterface
{
    public function obterSaldoSistema(int $conta, int $reduz, ?string $dataReferencia = null): ?float
    {
        $saldo = $this->firstValueWithFallback(
            function () use ($conta, $reduz, $dataReferencia) {
                $query = DB::table('tesouraria.saltes')
                    ->where('k13_conta', $conta)
                    ->where('k13_reduz', $reduz);

                if (!empty($dataReferencia)) {
                    $query->whereDate('k13_datvlr', '<=', $dataReferencia);
                }

                return $query
                    ->orderByDesc('k13_datvlr')
                    ->value(DB::raw('COALESCE(k13_vlratu, k13_saldo)'));
            },
            function () use ($conta, $reduz, $dataReferencia) {
                $query = DB::table('saltes')
                    ->where('k13_conta', $conta)
                    ->where('k13_reduz', $reduz);

                if (!empty($dataReferencia)) {
                    $query->whereDate('k13_datvlr', '<=', $dataReferencia);
                }

                return $query
                    ->orderByDesc('k13_datvlr')
                    ->value(DB::raw('COALESCE(k13_vlratu, k13_saldo)'));
            }
        );

        return $saldo !== null ? (float) $saldo : null;
    }

    private function firstValueWithFallback(callable $queryWithSchema, callable $queryWithoutSchema)
    {
        try {
            return $queryWithSchema();
        } catch (QueryException $exception) {
            return $queryWithoutSchema();
        }
    }
}

