<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Services\Financeiro\Relatorio\BalancoService;
use App\Services\Financeiro\Relatorio\DashboardExecutivoFinanceiroService;
use App\Services\Financeiro\Relatorio\DemonstracaoContabilService;
use App\Services\Financeiro\Relatorio\ExportacaoRelatoriosFinanceirosService;
use App\Services\Financeiro\Relatorio\PdfRendererInterface;
use App\Services\Financeiro\Relatorio\RelatorioFiscalService;
use PHPUnit\Framework\TestCase;

class ExportacaoRelatoriosFinanceirosServiceTest extends TestCase
{
    public function testExportaArquivosPdfECsv(): void
    {
        $dashboard = $this->createMock(DashboardExecutivoFinanceiroService::class);
        $balanco = $this->createMock(BalancoService::class);
        $demonstracao = $this->createMock(DemonstracaoContabilService::class);
        $fiscal = $this->createMock(RelatorioFiscalService::class);
        $pdf = $this->createMock(PdfRendererInterface::class);

        $balanco->method('gerar')->willReturn(['relatorios' => ['patrimonial' => ['ativo_total' => 100.0]]]);
        $demonstracao->method('gerar')->willReturn(['demonstracoes' => ['dvp' => ['resultado_patrimonial' => 20.0]]]);
        $fiscal->method('gerar')->willReturn(['relatorios' => ['rgf' => ['percentual_pessoal_sobre_rcl' => 50.0]]]);
        $dashboard->method('gerar')->willReturn([
            'painel_receitas' => ['realizado' => 100.0],
            'painel_despesas' => ['empenhadas' => 90.0],
            'execucao_orcamentaria' => ['percentual_execucao' => 90.0],
            'alertas' => [],
        ]);
        $pdf->method('render')->willReturn('%PDF-FAKE%');

        $diretorio = sys_get_temp_dir() . '/ecidade-export-test-' . uniqid('', true);
        $service = new ExportacaoRelatoriosFinanceirosService($dashboard, $balanco, $demonstracao, $fiscal, $pdf);
        $resultado = $service->exportar('2026-01-01', '2026-01-31', $diretorio);

        $this->assertFileExists($resultado['arquivo_planilha_csv']);
        $this->assertFileExists($resultado['arquivo_pdf']);

        @unlink($resultado['arquivo_planilha_csv']);
        @unlink($resultado['arquivo_pdf']);
        @rmdir($diretorio);
    }
}
