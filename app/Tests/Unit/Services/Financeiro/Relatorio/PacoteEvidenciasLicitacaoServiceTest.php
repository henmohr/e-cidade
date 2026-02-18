<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use App\Services\Financeiro\Relatorio\DashboardExecutivoFinanceiroService;
use App\Services\Financeiro\Relatorio\ExportacaoRelatoriosFinanceirosService;
use App\Services\Financeiro\Relatorio\PacoteEvidenciasLicitacaoService;
use PHPUnit\Framework\TestCase;

class PacoteEvidenciasLicitacaoServiceTest extends TestCase
{
    public function testGeraManifestoEResumoNoDiretorioInformado(): void
    {
        $exportacao = $this->createMock(ExportacaoRelatoriosFinanceirosService::class);
        $dashboard = $this->createMock(DashboardExecutivoFinanceiroService::class);
        $integracao = $this->createMock(IntegracaoGovernamentalStatusService::class);

        $exportacao->method('exportar')->willReturn([
            'arquivo_planilha_csv' => '/tmp/fake.csv',
            'arquivo_pdf' => '/tmp/fake.pdf',
        ]);

        $dashboard->method('gerar')->willReturn([
            'execucao_orcamentaria' => ['percentual_execucao' => 88.5],
            'alertas' => [],
        ]);

        $integracao->method('monitorarFalhas')->willReturn([
            'total_falhas' => 0,
            'codigos' => [],
        ]);

        $diretorio = sys_get_temp_dir() . '/ecidade-pacote-evidencias-' . uniqid('', true);

        $service = new PacoteEvidenciasLicitacaoService($exportacao, $dashboard, $integracao);
        $resultado = $service->gerar('2026-01-01', '2026-01-31', $diretorio, ['SICONFI']);

        $this->assertFileExists($resultado['manifesto']);
        $this->assertFileExists($resultado['resumo_markdown']);
        $this->assertSame('apto_para_banca', $resultado['status_recomendado']);

        @unlink($resultado['manifesto']);
        @unlink($resultado['resumo_markdown']);
        @rmdir($diretorio);
    }
}
