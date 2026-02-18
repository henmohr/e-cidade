<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\BalancoRepositoryInterface;
use App\Services\Financeiro\Relatorio\BalancoService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BalancoServiceTest extends TestCase
{
    public function testGeraTodosBalancosComPeriodo(): void
    {
        $repository = $this->createMock(BalancoRepositoryInterface::class);
        $repository->method('obterDadosBalancoPatrimonial')->willReturn([
            'ativo_total' => 1000.0,
            'passivo_total' => 300.0,
            'patrimonio_liquido' => 700.0,
        ]);
        $repository->method('obterDadosBalancoOrcamentario')->willReturn([
            'receita_prevista' => 1200.0,
            'receita_realizada' => 1100.0,
            'despesa_empenhada' => 800.0,
            'despesa_paga' => 700.0,
            'resultado_orcamentario' => 300.0,
        ]);
        $repository->method('obterDadosBalancoFinanceiro')->willReturn([
            'receitas_arrecadadas' => 1100.0,
            'despesas_pagas' => 700.0,
            'resultado_financeiro' => 400.0,
        ]);

        $service = new BalancoService($repository);
        $resultado = $service->gerar(BalancoService::TIPO_TODOS, '2026-01-01', '2026-01-31');

        $this->assertSame('todos', $resultado['tipo']);
        $this->assertArrayHasKey('patrimonial', $resultado['relatorios']);
        $this->assertArrayHasKey('orcamentario', $resultado['relatorios']);
        $this->assertArrayHasKey('financeiro', $resultado['relatorios']);
        $this->assertSame(700.0, $resultado['relatorios']['patrimonial']['patrimonio_liquido']);
    }

    public function testGeraSomenteBalancoPatrimonial(): void
    {
        $repository = $this->createMock(BalancoRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('obterDadosBalancoPatrimonial')
            ->willReturn([
                'ativo_total' => 500.0,
                'passivo_total' => 100.0,
                'patrimonio_liquido' => 400.0,
            ]);

        $repository->expects($this->never())->method('obterDadosBalancoOrcamentario');
        $repository->expects($this->never())->method('obterDadosBalancoFinanceiro');

        $service = new BalancoService($repository);
        $resultado = $service->gerar(BalancoService::TIPO_PATRIMONIAL);

        $this->assertCount(1, $resultado['relatorios']);
        $this->assertArrayHasKey('patrimonial', $resultado['relatorios']);
    }

    public function testLancaErroParaTipoInvalido(): void
    {
        $repository = $this->createMock(BalancoRepositoryInterface::class);
        $service = new BalancoService($repository);

        $this->expectException(InvalidArgumentException::class);
        $service->gerar('invalido');
    }
}
