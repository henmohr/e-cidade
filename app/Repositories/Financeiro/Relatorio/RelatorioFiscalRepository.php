<?php

namespace App\Repositories\Financeiro\Relatorio;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class RelatorioFiscalRepository implements RelatorioFiscalRepositoryInterface
{
    public function obterDadosRgf(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $receitaCorrenteLiquida = $this->somarColunaComFallback(
            'arrecad.arrecad',
            'arrecad',
            'k00_valor',
            'k00_data',
            $dataInicial,
            $dataFinal
        );

        $despesaPessoal = $this->somarColunaComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlrpag',
            'e60_data',
            $dataInicial,
            $dataFinal
        );

        $limiteLegalPessoal = round($receitaCorrenteLiquida * 0.54, 2);
        $percentualPessoal = $receitaCorrenteLiquida > 0
            ? round(($despesaPessoal / $receitaCorrenteLiquida) * 100, 2)
            : 0.0;

        return [
            'receita_corrente_liquida' => round($receitaCorrenteLiquida, 2),
            'despesa_total_pessoal' => round($despesaPessoal, 2),
            'limite_legal_pessoal' => $limiteLegalPessoal,
            'percentual_pessoal_sobre_rcl' => $percentualPessoal,
        ];
    }

    public function obterDadosRreo(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $receitaRealizada = $this->somarColunaComFallback(
            'arrecad.arrecad',
            'arrecad',
            'k00_valor',
            'k00_data',
            $dataInicial,
            $dataFinal
        );

        $despesaEmpenhada = $this->somarColunaComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlremp',
            'e60_data',
            $dataInicial,
            $dataFinal
        );

        $despesaLiquidada = $this->somarColunaComFallback(
            'empenho.empempenho',
            'empempenho',
            'e60_vlrliq',
            'e60_data',
            $dataInicial,
            $dataFinal
        );

        return [
            'receita_prevista' => round($receitaRealizada, 2),
            'receita_realizada' => round($receitaRealizada, 2),
            'despesa_empenhada' => round($despesaEmpenhada, 2),
            'despesa_liquidada' => round($despesaLiquidada, 2),
            'resultado_execucao' => round($receitaRealizada - $despesaEmpenhada, 2),
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
