<?php

namespace App\Repositories\Financeiro\Credor;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class FluxoDespesaCredorRepository implements FluxoDespesaCredorRepositoryInterface
{
    public function hasEmpenhoDoCredor(int $numeroEmpenho, int $numcgm): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.empempenho')
                ->where('e60_numemp', $numeroEmpenho)
                ->where('e60_numcgm', $numcgm)
                ->exists(),
            static fn () => DB::table('empempenho')
                ->where('e60_numemp', $numeroEmpenho)
                ->where('e60_numcgm', $numcgm)
                ->exists()
        );
    }

    public function hasAtestoDoEmpenho(int $numeroEmpenho): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.empnota')
                ->where('e69_numemp', $numeroEmpenho)
                ->exists(),
            static fn () => DB::table('empnota')
                ->where('e69_numemp', $numeroEmpenho)
                ->exists()
        );
    }

    public function hasPagamentoDoEmpenho(int $numeroEmpenho): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.empord')
                ->join('empenho.pagordem', 'e82_codord', '=', 'e50_codord')
                ->where('e50_numemp', $numeroEmpenho)
                ->exists(),
            static fn () => DB::table('empord')
                ->join('pagordem', 'e82_codord', '=', 'e50_codord')
                ->where('e50_numemp', $numeroEmpenho)
                ->exists()
        );
    }

    private function existsWithFallback(callable $queryWithSchema, callable $queryWithoutSchema): bool
    {
        try {
            return (bool) $queryWithSchema();
        } catch (QueryException $exception) {
            return (bool) $queryWithoutSchema();
        }
    }
}

