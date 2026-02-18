<?php

namespace App\Repositories\Financeiro\Receita;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ControleReceitasRepository implements ControleReceitasRepositoryInterface
{
    public function obterReceitasConsolidadas(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $dados = $this->getWithFallback(
            fn () => $this->buildQuery('caixa.arrecad', 'caixa.tabrec', $dataInicial, $dataFinal)->get(),
            fn () => $this->buildQuery('arrecad', 'tabrec', $dataInicial, $dataFinal)->get()
        );

        return array_map(static fn ($linha): array => (array) $linha, $dados->all());
    }

    private function buildQuery(string $tabelaArrecad, string $tabelaTabrec, ?string $dataInicial, ?string $dataFinal)
    {
        $query = DB::table("{$tabelaArrecad} as arrecad")
            ->join("{$tabelaTabrec} as tabrec", 'tabrec.k02_codigo', '=', 'arrecad.k00_receit')
            ->selectRaw('
                arrecad.k00_receit as codigo_receita,
                tabrec.k02_descr as descricao_curta,
                tabrec.k02_drecei as descricao_completa,
                SUM(COALESCE(arrecad.k00_valor, 0)) as valor_total
            ')
            ->groupBy('arrecad.k00_receit', 'tabrec.k02_descr', 'tabrec.k02_drecei')
            ->orderBy('arrecad.k00_receit');

        if (!empty($dataInicial)) {
            $query->whereDate('arrecad.k00_dtoper', '>=', $dataInicial);
        }
        if (!empty($dataFinal)) {
            $query->whereDate('arrecad.k00_dtoper', '<=', $dataFinal);
        }

        return $query;
    }

    private function getWithFallback(callable $queryWithSchema, callable $queryWithoutSchema)
    {
        try {
            return $queryWithSchema();
        } catch (QueryException $exception) {
            return $queryWithoutSchema();
        }
    }
}

