<?php

namespace App\Repositories\Financeiro\Credor;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CredorRepository implements CredorRepositoryInterface
{
    public function obterCredorPorCgm(int $numcgm): ?array
    {
        $credor = $this->firstWithFallback(
            static fn () => DB::table('protocolo.cgm as cgm')
                ->leftJoin('compras.pcforne as forne', 'forne.pc60_numcgm', '=', 'cgm.z01_numcgm')
                ->where('cgm.z01_numcgm', $numcgm)
                ->select([
                    'cgm.z01_numcgm',
                    'cgm.z01_nome',
                    'cgm.z01_cgccpf',
                    'cgm.z01_email',
                    'cgm.z01_ender',
                    'cgm.z01_numero',
                    'forne.pc60_numcgm as fornecedor_cgm',
                    'forne.pc60_bloqueado',
                    'forne.pc60_inscriestadual',
                    'forne.pc60_numeroregistro',
                    'forne.pc60_orgaoreg',
                    'forne.pc60_dtreg',
                ])
                ->first(),
            static fn () => DB::table('cgm')
                ->leftJoin('pcforne as forne', 'forne.pc60_numcgm', '=', 'cgm.z01_numcgm')
                ->where('cgm.z01_numcgm', $numcgm)
                ->select([
                    'cgm.z01_numcgm',
                    'cgm.z01_nome',
                    'cgm.z01_cgccpf',
                    'cgm.z01_email',
                    'cgm.z01_ender',
                    'cgm.z01_numero',
                    'forne.pc60_numcgm as fornecedor_cgm',
                    'forne.pc60_bloqueado',
                    'forne.pc60_inscriestadual',
                    'forne.pc60_numeroregistro',
                    'forne.pc60_orgaoreg',
                    'forne.pc60_dtreg',
                ])
                ->first()
        );

        if ($credor === null) {
            return null;
        }

        return (array) $credor;
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

