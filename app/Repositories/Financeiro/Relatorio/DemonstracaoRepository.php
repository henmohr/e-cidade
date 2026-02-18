<?php

namespace App\Repositories\Financeiro\Relatorio;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DemonstracaoRepository implements DemonstracaoRepositoryInterface
{
    public function obterDadosDvp(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $variacoesAtivas = $this->somarColunaComFallback(
            'arrecad.arrecad',
            'arrecad',
            'k00_valor',
            'k00_data',
            $dataInicial,
            $dataFinal
        );

        $variacoesPassivas = $this->somarColunaComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlremp',
            'e60_data',
            $dataInicial,
            $dataFinal
        );

        return [
            'variacoes_ativas' => round($variacoesAtivas, 2),
            'variacoes_passivas' => round($variacoesPassivas, 2),
            'resultado_patrimonial' => round($variacoesAtivas - $variacoesPassivas, 2),
        ];
    }

    public function obterDadosDfc(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $entradasOperacionais = $this->somarColunaComFallback(
            'arrecad.arrecad',
            'arrecad',
            'k00_valor',
            'k00_data',
            $dataInicial,
            $dataFinal
        );

        $saidasOperacionais = $this->somarColunaComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlrpag',
            'e60_data',
            $dataInicial,
            $dataFinal
        );

        return [
            'entradas_operacionais' => round($entradasOperacionais, 2),
            'saidas_operacionais' => round($saidasOperacionais, 2),
            'variacao_liquida_caixa' => round($entradasOperacionais - $saidasOperacionais, 2),
        ];
    }

    private function somarColunaComFallback(
        string $tabelaComSchema,
        string $tabelaSemSchema,
        string $colunaValor,
        string $colunaData,
        ?string $dataInicial,
        ?string $dataFinal
    ): float {
        return $this->executarComFallback(
            function () use ($tabelaComSchema, $colunaValor, $colunaData, $dataInicial, $dataFinal): float {
                return $this->somarColuna($tabelaComSchema, $colunaValor, $colunaData, $dataInicial, $dataFinal);
            },
            function () use ($tabelaSemSchema, $colunaValor, $colunaData, $dataInicial, $dataFinal): float {
                return $this->somarColuna($tabelaSemSchema, $colunaValor, $colunaData, $dataInicial, $dataFinal);
            }
        );
    }

    private function somarColuna(
        string $tabela,
        string $colunaValor,
        string $colunaData,
        ?string $dataInicial,
        ?string $dataFinal
    ): float {
        $query = DB::table($tabela)->selectRaw('COALESCE(SUM(' . $colunaValor . '), 0) AS valor');

        if ($dataInicial !== null && $dataInicial !== '') {
            $query->whereDate($colunaData, '>=', $dataInicial);
        }

        if ($dataFinal !== null && $dataFinal !== '') {
            $query->whereDate($colunaData, '<=', $dataFinal);
        }

        $linha = $query->first();

        return isset($linha->valor) ? (float) $linha->valor : 0.0;
    }

    private function executarComFallback(callable $consultaComSchema, callable $consultaSemSchema): float
    {
        try {
            return (float) $consultaComSchema();
        } catch (QueryException $exception) {
            try {
                return (float) $consultaSemSchema();
            } catch (QueryException $nestedException) {
                return 0.0;
            }
        }
    }
}
