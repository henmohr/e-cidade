<?php

namespace App\Services\Financeiro\Relatorio;

use RuntimeException;

class ExportacaoRelatoriosFinanceirosService
{
    private DashboardExecutivoFinanceiroService $dashboardService;
    private BalancoService $balancoService;
    private DemonstracaoContabilService $demonstracaoService;
    private RelatorioFiscalService $fiscalService;
    private PdfRendererInterface $pdfRenderer;

    public function __construct(
        ?DashboardExecutivoFinanceiroService $dashboardService = null,
        ?BalancoService $balancoService = null,
        ?DemonstracaoContabilService $demonstracaoService = null,
        ?RelatorioFiscalService $fiscalService = null,
        ?PdfRendererInterface $pdfRenderer = null
    ) {
        $this->dashboardService = $dashboardService ?? new DashboardExecutivoFinanceiroService();
        $this->balancoService = $balancoService ?? new BalancoService();
        $this->demonstracaoService = $demonstracaoService ?? new DemonstracaoContabilService();
        $this->fiscalService = $fiscalService ?? new RelatorioFiscalService();
        $this->pdfRenderer = $pdfRenderer ?? new MpdfPdfRenderer();
    }

    /**
     * @return array<string, string>
     */
    public function exportar(?string $dataInicial = null, ?string $dataFinal = null, ?string $diretorioBase = null): array
    {
        $diretorio = $diretorioBase ?: storage_path('app/financeiro/relatorios');
        if (!is_dir($diretorio) && !mkdir($diretorio, 0775, true) && !is_dir($diretorio)) {
            throw new RuntimeException('Nao foi possivel criar o diretorio de exportacao.');
        }

        $balancos = $this->balancoService->gerar(BalancoService::TIPO_TODOS, $dataInicial, $dataFinal);
        $demonstracoes = $this->demonstracaoService->gerar(DemonstracaoContabilService::TIPO_TODAS, $dataInicial, $dataFinal);
        $fiscais = $this->fiscalService->gerar(RelatorioFiscalService::TIPO_TODOS, RelatorioFiscalService::PERIODICIDADE_QUADRIMESTRAL, $dataInicial, $dataFinal);
        $dashboard = $this->dashboardService->gerar($dataInicial, $dataFinal);

        $referencia = date('Ymd_His');
        $arquivoCsv = rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'relatorios_financeiros_' . $referencia . '.csv';
        $arquivoPdf = rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'relatorios_financeiros_' . $referencia . '.pdf';

        file_put_contents($arquivoCsv, $this->gerarCsv($balancos, $demonstracoes, $fiscais, $dashboard));

        $html = $this->gerarHtml($balancos, $demonstracoes, $fiscais, $dashboard);
        file_put_contents($arquivoPdf, $this->pdfRenderer->render($html));

        return [
            'arquivo_planilha_csv' => $arquivoCsv,
            'arquivo_pdf' => $arquivoPdf,
        ];
    }

    /**
     * @param array<string, mixed> $balancos
     * @param array<string, mixed> $demonstracoes
     * @param array<string, mixed> $fiscais
     * @param array<string, mixed> $dashboard
     */
    private function gerarCsv(array $balancos, array $demonstracoes, array $fiscais, array $dashboard): string
    {
        $linhas = [
            ['secao', 'indicador', 'valor'],
        ];

        $this->adicionarSecaoCsv($linhas, 'BALANCOS', $balancos['relatorios'] ?? []);
        $this->adicionarSecaoCsv($linhas, 'DEMONSTRACOES', $demonstracoes['demonstracoes'] ?? []);
        $this->adicionarSecaoCsv($linhas, 'FISCAIS', $fiscais['relatorios'] ?? []);
        $this->adicionarSecaoCsv($linhas, 'DASHBOARD', [
            'painel_receitas' => $dashboard['painel_receitas'] ?? [],
            'painel_despesas' => $dashboard['painel_despesas'] ?? [],
            'execucao_orcamentaria' => $dashboard['execucao_orcamentaria'] ?? [],
        ]);

        $conteudo = '';
        foreach ($linhas as $linha) {
            $conteudo .= sprintf("%s,%s,%s\n", $linha[0], $linha[1], $linha[2]);
        }

        return $conteudo;
    }

    /**
     * @param array<int, array<int, string>> $linhas
     * @param array<string, mixed> $dados
     */
    private function adicionarSecaoCsv(array &$linhas, string $secao, array $dados): void
    {
        foreach ($dados as $grupo => $valores) {
            if (!is_array($valores)) {
                continue;
            }

            foreach ($valores as $chave => $valor) {
                if (is_array($valor)) {
                    continue;
                }

                $linhas[] = [
                    $secao . ':' . strtoupper((string) $grupo),
                    (string) $chave,
                    (string) $valor,
                ];
            }
        }
    }

    /**
     * @param array<string, mixed> $balancos
     * @param array<string, mixed> $demonstracoes
     * @param array<string, mixed> $fiscais
     * @param array<string, mixed> $dashboard
     */
    private function gerarHtml(array $balancos, array $demonstracoes, array $fiscais, array $dashboard): string
    {
        return '<h1>Relatorios Financeiros</h1>'
            . '<h2>Balancos</h2><pre>' . htmlspecialchars((string) json_encode($balancos['relatorios'] ?? [], JSON_PRETTY_PRINT)) . '</pre>'
            . '<h2>Demonstracoes</h2><pre>' . htmlspecialchars((string) json_encode($demonstracoes['demonstracoes'] ?? [], JSON_PRETTY_PRINT)) . '</pre>'
            . '<h2>Fiscais</h2><pre>' . htmlspecialchars((string) json_encode($fiscais['relatorios'] ?? [], JSON_PRETTY_PRINT)) . '</pre>'
            . '<h2>Dashboard Executivo</h2><pre>' . htmlspecialchars((string) json_encode($dashboard, JSON_PRETTY_PRINT)) . '</pre>';
    }
}
