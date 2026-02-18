<?php

namespace App\Repositories\Financeiro\ExecucaoOrcamentaria;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CicloDespesaRepository implements CicloDespesaRepositoryInterface
{
    public function getNumeroEmpenhoPorOrdemPagamento(int $codigoOrdem): ?int
    {
        $numeroEmpenho = $this->firstValueWithFallback(
            static fn () => DB::table('empenho.pagordem')
                ->where('e50_codord', $codigoOrdem)
                ->value('e50_numemp'),
            static fn () => DB::table('pagordem')
                ->where('e50_codord', $codigoOrdem)
                ->value('e50_numemp')
        );

        return $numeroEmpenho !== null ? (int) $numeroEmpenho : null;
    }

    public function hasFixacao(int $ano, int $codigoDotacao): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('orcamento.orcdotacao')
                ->where('o58_anousu', $ano)
                ->where('o58_coddot', $codigoDotacao)
                ->exists(),
            static fn () => DB::table('orcdotacao')
                ->where('o58_anousu', $ano)
                ->where('o58_coddot', $codigoDotacao)
                ->exists()
        );
    }

    public function hasFixacaoParaEmpenho(int $numeroEmpenho): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.empempenho as empenho')
                ->join('orcamento.orcdotacao as dotacao', static function ($join): void {
                    $join->on('dotacao.o58_coddot', '=', 'empenho.e60_coddot');
                    $join->on('dotacao.o58_anousu', '=', 'empenho.e60_anousu');
                })
                ->where('empenho.e60_numemp', $numeroEmpenho)
                ->exists(),
            static fn () => DB::table('empempenho as empenho')
                ->join('orcdotacao as dotacao', static function ($join): void {
                    $join->on('dotacao.o58_coddot', '=', 'empenho.e60_coddot');
                    $join->on('dotacao.o58_anousu', '=', 'empenho.e60_anousu');
                })
                ->where('empenho.e60_numemp', $numeroEmpenho)
                ->exists()
        );
    }

    public function getSaldoDisponivelDotacao(int $ano, int $codigoDotacao): ?float
    {
        $saldo = $this->firstValueWithFallback(
            static fn () => DB::table('orcamento.orcdotacao as dotacao')
                ->leftJoin('empenho.empempenho as empenho', static function ($join): void {
                    $join->on('empenho.e60_anousu', '=', 'dotacao.o58_anousu');
                    $join->on('empenho.e60_coddot', '=', 'dotacao.o58_coddot');
                })
                ->where('dotacao.o58_anousu', $ano)
                ->where('dotacao.o58_coddot', $codigoDotacao)
                ->selectRaw('dotacao.o58_valor - COALESCE(SUM(COALESCE(empenho.e60_vlrutilizado, 0)), 0) as saldo_disponivel')
                ->groupBy('dotacao.o58_valor')
                ->value('saldo_disponivel'),
            static fn () => DB::table('orcdotacao as dotacao')
                ->leftJoin('empempenho as empenho', static function ($join): void {
                    $join->on('empenho.e60_anousu', '=', 'dotacao.o58_anousu');
                    $join->on('empenho.e60_coddot', '=', 'dotacao.o58_coddot');
                })
                ->where('dotacao.o58_anousu', $ano)
                ->where('dotacao.o58_coddot', $codigoDotacao)
                ->selectRaw('dotacao.o58_valor - COALESCE(SUM(COALESCE(empenho.e60_vlrutilizado, 0)), 0) as saldo_disponivel')
                ->groupBy('dotacao.o58_valor')
                ->value('saldo_disponivel')
        );

        return $saldo !== null ? (float) $saldo : null;
    }

    public function hasEmpenho(int $numeroEmpenho): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.empempenho')
                ->where('e60_numemp', $numeroEmpenho)
                ->exists(),
            static fn () => DB::table('empempenho')
                ->where('e60_numemp', $numeroEmpenho)
                ->exists()
        );
    }

    public function hasLiquidacao(int $numeroEmpenho): bool
    {
        return $this->existsWithFallback(
            static fn () => DB::table('empenho.pagordem')
                ->where('e50_numemp', $numeroEmpenho)
                ->exists(),
            static fn () => DB::table('pagordem')
                ->where('e50_numemp', $numeroEmpenho)
                ->exists()
        );
    }

    public function hasPagamento(int $numeroEmpenho): bool
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

    private function firstValueWithFallback(callable $queryWithSchema, callable $queryWithoutSchema)
    {
        try {
            return $queryWithSchema();
        } catch (QueryException $exception) {
            return $queryWithoutSchema();
        }
    }
}
