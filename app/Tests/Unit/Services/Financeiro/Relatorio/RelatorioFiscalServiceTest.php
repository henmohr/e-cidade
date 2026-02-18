<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\RelatorioFiscalRepositoryInterface;
use App\Services\Financeiro\Relatorio\RelatorioFiscalService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RelatorioFiscalServiceTest extends TestCase
{
    public function testGeraRgfERreoComPeriodicidadeQuadrimestral(): void
    {
        $repository = $this->createMock(RelatorioFiscalRepositoryInterface::class);
        $repository->method('obterDadosRgf')->willReturn([
            'receita_corrente_liquida' => 1000.0,
            'despesa_total_pessoal' => 500.0,
            'limite_legal_pessoal' => 540.0,
            'percentual_pessoal_sobre_rcl' => 50.0,
        ]);
        $repository->method('obterDadosRreo')->willReturn([
            'receita_prevista' => 1200.0,
            'receita_realizada' => 1100.0,
            'despesa_empenhada' => 900.0,
            'despesa_liquidada' => 850.0,
            'resultado_execucao' => 200.0,
        ]);

        $service = new RelatorioFiscalService($repository);
        $resultado = $service->gerar('todos', 'quadrimestral', '2026-01-01', '2026-04-30');

        $this->assertSame('todos', $resultado['tipo']);
        $this->assertSame('quadrimestral', $resultado['periodicidade']);
        $this->assertArrayHasKey('rgf', $resultado['relatorios']);
        $this->assertArrayHasKey('rreo', $resultado['relatorios']);
    }

    public function testGeraSomenteRgf(): void
    {
        $repository = $this->createMock(RelatorioFiscalRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('obterDadosRgf')
            ->willReturn([
                'receita_corrente_liquida' => 900.0,
                'despesa_total_pessoal' => 450.0,
                'limite_legal_pessoal' => 486.0,
                'percentual_pessoal_sobre_rcl' => 50.0,
            ]);

        $repository->expects($this->never())->method('obterDadosRreo');

        $service = new RelatorioFiscalService($repository);
        $resultado = $service->gerar('rgf', 'mensal');

        $this->assertCount(1, $resultado['relatorios']);
        $this->assertArrayHasKey('rgf', $resultado['relatorios']);
    }

    public function testFalhaParaPeriodicidadeInvalida(): void
    {
        $repository = $this->createMock(RelatorioFiscalRepositoryInterface::class);
        $service = new RelatorioFiscalService($repository);

        $this->expectException(InvalidArgumentException::class);
        $service->gerar('todos', 'semanal');
    }
}
