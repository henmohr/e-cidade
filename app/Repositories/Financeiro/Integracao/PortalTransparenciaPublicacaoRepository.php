<?php

namespace App\Repositories\Financeiro\Integracao;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PortalTransparenciaPublicacaoRepository implements PortalTransparenciaPublicacaoRepositoryInterface
{
    public function obterDadosReceitas(?string $dataReferencia = null): array
    {
        return $this->queryComFallback(
            'arrecad.arrecad',
            'arrecad',
            'k00_valor',
            'k00_data',
            $dataReferencia
        );
    }

    public function obterDadosDespesas(?string $dataReferencia = null): array
    {
        return $this->queryComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlremp',
            'e60_data',
            $dataReferencia
        );
    }

    public function obterDadosContratos(?string $dataReferencia = null): array
    {
        return $this->queryComFallback(
            'licitacao.conlancamento',
            'conlancamento',
            'l20_valor',
            'l20_data',
            $dataReferencia
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function queryComFallback(
        string $tabelaComSchema,
        string $tabelaSemSchema,
        string $colunaValor,
        string $colunaData,
        ?string $dataReferencia = null
    ): array {
        $consulta = function (string $tabela) use ($colunaValor, $colunaData, $dataReferencia): array {
            $query = DB::table($tabela)
                ->selectRaw('SUM(COALESCE(' . $colunaValor . ', 0)) AS valor_total, COUNT(*) AS total_registros');

            if ($dataReferencia !== null && $dataReferencia !== '') {
                $query->whereDate($colunaData, '<=', $dataReferencia);
            }

            $linha = $query->first();

            return [[
                'valor_total' => isset($linha->valor_total) ? (float) $linha->valor_total : 0.0,
                'total_registros' => isset($linha->total_registros) ? (int) $linha->total_registros : 0,
            ]];
        };

        try {
            return $consulta($tabelaComSchema);
        } catch (QueryException $exception) {
            try {
                return $consulta($tabelaSemSchema);
            } catch (QueryException $nestedException) {
                return [[
                    'valor_total' => 0.0,
                    'total_registros' => 0,
                ]];
            }
        }
    }
}
