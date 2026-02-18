<?php

namespace App\Services\Financeiro\Relatorio;

use App\Services\Financeiro\Receita\ControleReceitasService;
use App\Services\Financeiro\Tesouraria\RestosAPagarService;

class DashboardExecutivoFinanceiroService
{
    private ControleReceitasService $controleReceitasService;
    private BalancoService $balancoService;
    private RelatorioFiscalService $relatorioFiscalService;
    private RestosAPagarService $restosAPagarService;

    public function __construct(
        ?ControleReceitasService $controleReceitasService = null,
        ?BalancoService $balancoService = null,
        ?RelatorioFiscalService $relatorioFiscalService = null,
        ?RestosAPagarService $restosAPagarService = null
    ) {
        $this->controleReceitasService = $controleReceitasService ?? new ControleReceitasService();
        $this->balancoService = $balancoService ?? new BalancoService();
        $this->relatorioFiscalService = $relatorioFiscalService ?? new RelatorioFiscalService();
        $this->restosAPagarService = $restosAPagarService ?? new RestosAPagarService();
    }

    /**
     * @return array<string, mixed>
     */
    public function gerar(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $painelReceitas = $this->controleReceitasService->consolidar($dataInicial, $dataFinal);
        $balancoOrcamentario = $this->balancoService->gerar(BalancoService::TIPO_ORCAMENTARIO, $dataInicial, $dataFinal);
        $fiscal = $this->relatorioFiscalService->gerar(
            RelatorioFiscalService::TIPO_RGF,
            RelatorioFiscalService::PERIODICIDADE_MENSAL,
            $dataInicial,
            $dataFinal
        );
        $restos = $this->restosAPagarService->obterResumo($this->resolverAno($dataFinal));

        $orcamentario = $balancoOrcamentario['relatorios']['orcamentario'] ?? [];
        $rgf = $fiscal['relatorios']['rgf'] ?? [];

        $previsto = (float) ($orcamentario['receita_prevista'] ?? 0);
        $realizado = (float) ($orcamentario['receita_realizada'] ?? 0);
        $execucaoPercentual = $previsto > 0 ? round(($realizado / $previsto) * 100, 2) : 0.0;

        return [
            'gerado_em' => date('c'),
            'periodo' => [
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
            'painel_receitas' => [
                'previsto' => round($previsto, 2),
                'realizado' => round($realizado, 2),
                'percentual_execucao' => $execucaoPercentual,
                'totais_consolidados' => $painelReceitas['totais'] ?? [],
            ],
            'painel_despesas' => [
                'empenhadas' => round((float) ($orcamentario['despesa_empenhada'] ?? 0), 2),
                'liquidadas' => round((float) ($orcamentario['despesa_empenhada'] ?? 0), 2),
                'pagas' => round((float) ($orcamentario['despesa_paga'] ?? 0), 2),
            ],
            'execucao_orcamentaria' => [
                'percentual_execucao' => $execucaoPercentual,
                'resultado_orcamentario' => round((float) ($orcamentario['resultado_orcamentario'] ?? 0), 2),
            ],
            'alertas' => $this->montarAlertas($orcamentario, $rgf, $restos),
        ];
    }

    /**
     * @param array<string, float|int|string> $orcamentario
     * @param array<string, float|int|string> $rgf
     * @param array<string, mixed> $restos
     * @return array<int, array<string, string|float>>
     */
    private function montarAlertas(array $orcamentario, array $rgf, array $restos): array
    {
        $alertas = [];

        $percentualPessoal = (float) ($rgf['percentual_pessoal_sobre_rcl'] ?? 0);
        if ($percentualPessoal > 54) {
            $alertas[] = [
                'tipo' => 'limite_pessoal',
                'severidade' => 'alto',
                'mensagem' => 'Despesa com pessoal acima do limite legal da LRF.',
                'valor' => $percentualPessoal,
            ];
        }

        $restosNaoProcessados = (float) ($restos['restos_nao_processados'] ?? 0);
        if ($restosNaoProcessados > 0) {
            $alertas[] = [
                'tipo' => 'restos_a_pagar',
                'severidade' => 'medio',
                'mensagem' => 'Existem restos a pagar nao processados pendentes.',
                'valor' => $restosNaoProcessados,
            ];
        }

        $resultado = (float) ($orcamentario['resultado_orcamentario'] ?? 0);
        if ($resultado < 0) {
            $alertas[] = [
                'tipo' => 'resultado_orcamentario',
                'severidade' => 'alto',
                'mensagem' => 'Resultado orcamentario negativo no periodo selecionado.',
                'valor' => $resultado,
            ];
        }

        return $alertas;
    }

    private function resolverAno(?string $dataFinal): int
    {
        if ($dataFinal === null || $dataFinal === '') {
            return (int) date('Y');
        }

        return (int) substr($dataFinal, 0, 4);
    }
}
