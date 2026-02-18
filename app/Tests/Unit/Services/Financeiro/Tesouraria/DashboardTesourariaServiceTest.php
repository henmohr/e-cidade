<?php

namespace App\Tests\Unit\Services\Financeiro\Tesouraria;

use App\Services\Financeiro\Tesouraria\DashboardTesourariaService;
use App\Services\Financeiro\Tesouraria\FluxoCaixaService;
use App\Services\Financeiro\Tesouraria\RestosAPagarService;
use PHPUnit\Framework\TestCase;

class DashboardTesourariaServiceTest extends TestCase
{
    public function testGeraDashboardComAlertasQuandoHaPendencias(): void
    {
        $fluxoService = $this->createMock(FluxoCaixaService::class);
        $fluxoService->method('projetar7Dias')->willReturn([
            'saldo_inicial' => 1000.0,
            'saldo_final_previsto' => 800.0,
            'menor_saldo_previsto' => -10.0,
            'dias' => [],
        ]);

        $restosService = $this->createMock(RestosAPagarService::class);
        $restosService->method('obterResumo')->willReturn([
            'ano' => 2026,
            'restos_processados' => 200.0,
            'restos_nao_processados' => 100.0,
            'restos_total' => 300.0,
        ]);

        $service = new DashboardTesourariaService($fluxoService, $restosService);
        $dashboard = $service->gerar(1, 10, 2026, '2026-02-18');

        $this->assertNotEmpty($dashboard['alertas']);
        $this->assertSame(1000.0, $dashboard['saldo_atual']);
    }

    public function testGeraDashboardSemAlertasQuandoCenarioEstavel(): void
    {
        $fluxoService = $this->createMock(FluxoCaixaService::class);
        $fluxoService->method('projetar7Dias')->willReturn([
            'saldo_inicial' => 1000.0,
            'saldo_final_previsto' => 1200.0,
            'menor_saldo_previsto' => 950.0,
            'dias' => [],
        ]);

        $restosService = $this->createMock(RestosAPagarService::class);
        $restosService->method('obterResumo')->willReturn([
            'ano' => 2026,
            'restos_processados' => 0.0,
            'restos_nao_processados' => 0.0,
            'restos_total' => 0.0,
        ]);

        $service = new DashboardTesourariaService($fluxoService, $restosService);
        $dashboard = $service->gerar(1, 10, 2026, '2026-02-18');

        $this->assertEmpty($dashboard['alertas']);
        $this->assertSame(1200.0, $dashboard['projecao_7_dias']['saldo_final_previsto']);
    }
}

