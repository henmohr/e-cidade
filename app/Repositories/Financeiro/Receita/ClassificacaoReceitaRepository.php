<?php

namespace App\Repositories\Financeiro\Receita;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ClassificacaoReceitaRepository implements ClassificacaoReceitaRepositoryInterface
{
    public function obterReceitaPorCodigo(int $codigoReceita): ?array
    {
        $receita = $this->firstWithFallback(
            static fn () => DB::table('caixa.tabrec')
                ->where('k02_codigo', $codigoReceita)
                ->select(['k02_codigo', 'k02_descr', 'k02_drecei', 'k02_tipo', 'k02_tabrectipo'])
                ->first(),
            static fn () => DB::table('tabrec')
                ->where('k02_codigo', $codigoReceita)
                ->select(['k02_codigo', 'k02_descr', 'k02_drecei', 'k02_tipo', 'k02_tabrectipo'])
                ->first()
        );

        return $receita ? (array) $receita : null;
    }

    private function firstWithFallback(callable $queryWithSchema, callable $queryWithoutSchema)
    {
        try {
            return $queryWithSchema();
        } catch (QueryException $exception) {
            return $queryWithoutSchema();
        }
    }
}

