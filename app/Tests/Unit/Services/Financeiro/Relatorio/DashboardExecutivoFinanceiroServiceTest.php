<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Services\Financeiro\Receita\ControleReceitasService;
use App\Services\Financeiro\Relatorio\BalancoService;
use App\Services\Financeiro\Relatorio\DashboardExecutivoFinanceiroService;
use App\Services\Financeiro\Relatorio\RelatorioFiscalService;
use App\Services\Financeiro\Tesouraria\RestosAPagarService;
use PHPUnit\Framework\TestCase;

class DashboardExecutivoFinanceiroServiceTest extends TestCase
{
    public function testGeraPainelComAlertas(): void
    {
        $receitas = $this->createMock(ControleReceitasService::class);
        $balanco = $this->createMock(BalancoService::class);
        $fiscal = $this->createMock(RelatorioFiscalService::class);
        $restos = $this->createMock(RestosAPagarService::class);

        $receitas->method('consolidar')->willReturn([
            'totais' => ['tributarias' => 1000.0],
        ]);

        $balanco->method('gerar')->willReturn([
            'relatorios' => [
                'orcamentario' => [
                    'receita_prevista' => 1000.0,
                    'receita_realizada' => 800.0,
                    'despesa_empenhada' => 900.0,
                    'despesa_paga' => 700.0,
                    'resultado_orcamentario' => -100.0,
                ],
            ],
        ]);

        $fiscal->method('gerar')->willReturn([
            'relatorios' => [
                'rgf' => [
                    'percentual_pessoal_sobre_rcl' => 58.0,
                ],
            ],
        ]);

        $restos->method('obterResumo')->willReturn([
            'restos_nao_processados' => 120.0,
        ]);

        $service = new DashboardExecutivoFinanceiroService($receitas, $balanco, $fiscal, $restos);
        $resultado = $service->gerar('2026-01-01', '2026-01-31');

        $this->assertSame(80.0, $resultado['painel_receitas']['percentual_execucao']);
        $this->assertCount(3, $resultado['alertas']);
    }
}
