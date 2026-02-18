<?php

namespace App\Services\Financeiro\Relatorio;

use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use RuntimeException;

class PacoteEvidenciasLicitacaoService
{
    private ExportacaoRelatoriosFinanceirosService $exportacaoService;
    private DashboardExecutivoFinanceiroService $dashboardService;
    private IntegracaoGovernamentalStatusService $integracaoStatusService;

    public function __construct(
        ?ExportacaoRelatoriosFinanceirosService $exportacaoService = null,
        ?DashboardExecutivoFinanceiroService $dashboardService = null,
        ?IntegracaoGovernamentalStatusService $integracaoStatusService = null
    ) {
        $this->exportacaoService = $exportacaoService ?? new ExportacaoRelatoriosFinanceirosService();
        $this->dashboardService = $dashboardService ?? new DashboardExecutivoFinanceiroService();
        $this->integracaoStatusService = $integracaoStatusService ?? new IntegracaoGovernamentalStatusService();
    }

    /**
     * @param array<int, string>|null $sistemasIntegracao
     * @return array<string, mixed>
     */
    public function gerar(
        ?string $dataInicial = null,
        ?string $dataFinal = null,
        ?string $diretorioBase = null,
        ?array $sistemasIntegracao = null
    ): array {
        $sistemas = $sistemasIntegracao ?: ['SICONFI', 'TCE_PR', 'PORTAL_TRANSPARENCIA'];
        $timestamp = date('Ymd_His');
        $diretorio = $diretorioBase ?: storage_path('app/financeiro/evidencias_licitacao/' . $timestamp);

        if (!is_dir($diretorio) && !mkdir($diretorio, 0775, true) && !is_dir($diretorio)) {
            throw new RuntimeException('Nao foi possivel criar diretorio do pacote de evidencias.');
        }

        $arquivosExportados = $this->exportacaoService->exportar($dataInicial, $dataFinal, $diretorio);
        $dashboard = $this->dashboardService->gerar($dataInicial, $dataFinal);

        $integracoes = [];
        $totalFalhasIntegracao = 0;

        foreach ($sistemas as $sistema) {
            $monitoramento = $this->integracaoStatusService->monitorarFalhas($sistema, 100);
            $integracoes[] = [
                'sistema' => $sistema,
                'total_falhas' => (int) ($monitoramento['total_falhas'] ?? 0),
                'codigos' => $monitoramento['codigos'] ?? [],
            ];
            $totalFalhasIntegracao += (int) ($monitoramento['total_falhas'] ?? 0);
        }

        $manifesto = [
            'gerado_em' => date('c'),
            'periodo' => [
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
            'arquivos_exportados' => $arquivosExportados,
            'dashboard_resumo' => [
                'execucao_orcamentaria' => $dashboard['execucao_orcamentaria'] ?? [],
                'total_alertas' => count($dashboard['alertas'] ?? []),
            ],
            'integracoes' => $integracoes,
            'indicadores_pacote' => [
                'pendencias_homologacao_externa' => $totalFalhasIntegracao,
                'status_recomendado' => $totalFalhasIntegracao > 0 ? 'apto_com_ressalvas' : 'apto_para_banca',
            ],
        ];

        $arquivoManifesto = rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'manifesto_evidencias.json';
        $arquivoResumo = rtrim($diretorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'resumo_evidencias.md';

        file_put_contents(
            $arquivoManifesto,
            (string) json_encode($manifesto, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        file_put_contents($arquivoResumo, $this->gerarResumoMarkdown($manifesto));

        return [
            'diretorio' => $diretorio,
            'manifesto' => $arquivoManifesto,
            'resumo_markdown' => $arquivoResumo,
            'status_recomendado' => $manifesto['indicadores_pacote']['status_recomendado'],
        ];
    }

    /**
     * @param array<string, mixed> $manifesto
     */
    private function gerarResumoMarkdown(array $manifesto): string
    {
        $status = (string) ($manifesto['indicadores_pacote']['status_recomendado'] ?? 'apto_com_ressalvas');
        $totalPendencias = (int) ($manifesto['indicadores_pacote']['pendencias_homologacao_externa'] ?? 0);

        $linhas = [
            '# Resumo de Evidencias da Licitacao',
            '',
            '- Gerado em: ' . (string) ($manifesto['gerado_em'] ?? ''),
            '- Status recomendado: ' . $status,
            '- Pendencias de homologacao externa: ' . $totalPendencias,
            '',
            '## Integracoes monitoradas',
        ];

        foreach (($manifesto['integracoes'] ?? []) as $integracao) {
            $linhas[] = '- ' . (string) ($integracao['sistema'] ?? 'N/A') . ': ' . (int) ($integracao['total_falhas'] ?? 0) . ' falha(s)';
        }

        $linhas[] = '';
        $linhas[] = '## Arquivos do pacote';
        $linhas[] = '- Manifesto JSON: `manifesto_evidencias.json`';
        $linhas[] = '- Resumo: `resumo_evidencias.md`';
        $linhas[] = '- Exportacao CSV/PDF: conforme campos em `arquivos_exportados` no manifesto';

        return implode("\n", $linhas) . "\n";
    }
}
