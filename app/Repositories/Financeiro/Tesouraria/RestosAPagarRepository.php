<?php

namespace App\Repositories\Financeiro\Tesouraria;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RestosAPagarRepository implements RestosAPagarRepositoryInterface
{
    public function obterTotais(?int $ano = null): array
    {
        $resultado = $this->firstValueWithFallback(
            function () use ($ano): object {
                $query = DB::table('empenho.empempenho');

                if ($ano !== null) {
                    $query->where('e60_anousu', $ano);
                }

                return $query->selectRaw('
                    COALESCE(SUM(GREATEST(COALESCE(e60_vlrliq, 0) - COALESCE(e60_vlrpag, 0), 0)), 0) AS restos_processados,
                    COALESCE(SUM(GREATEST(COALESCE(e60_vlremp, 0) - COALESCE(e60_vlrliq, 0), 0)), 0) AS restos_nao_processados
                ')->first();
            },
            function () use ($ano): object {
                $query = DB::table('empempenho');

                if ($ano !== null) {
                    $query->where('e60_anousu', $ano);
                }

                return $query->selectRaw('
                    COALESCE(SUM(GREATEST(COALESCE(e60_vlrliq, 0) - COALESCE(e60_vlrpag, 0), 0)), 0) AS restos_processados,
                    COALESCE(SUM(GREATEST(COALESCE(e60_vlremp, 0) - COALESCE(e60_vlrliq, 0), 0)), 0) AS restos_nao_processados
                ')->first();
            }
        );

        return [
            'restos_processados' => isset($resultado->restos_processados) ? (float) $resultado->restos_processados : 0.0,
            'restos_nao_processados' => isset($resultado->restos_nao_processados) ? (float) $resultado->restos_nao_processados : 0.0,
        ];
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

