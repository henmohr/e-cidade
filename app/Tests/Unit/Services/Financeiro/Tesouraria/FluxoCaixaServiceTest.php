<?php

namespace App\Tests\Unit\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\FluxoCaixaRepositoryInterface;
use App\Services\Financeiro\Tesouraria\FluxoCaixaService;
use LogicException;
use PHPUnit\Framework\TestCase;

class FluxoCaixaServiceTest extends TestCase
{
    public function testGeraPrevisaoDeSeteDiasComSaldoFinalProjetado(): void
    {
        $repository = $this->createMock(FluxoCaixaRepositoryInterface::class);
        $repository->method('obterSaldoAtual')->willReturn(1000.00);
        $repository->method('obterEntradasPrevistasMes')->willReturn(3100.00);
        $repository->method('obterSaidasPrevistasMes')->willReturn(1550.00);

        $service = new FluxoCaixaService($repository);
        $resultado = $service->projetar7Dias(1, 10, '2026-02-18');

        $this->assertCount(7, $resultado['dias']);
        $this->assertGreaterThan(1000.00, $resultado['saldo_final_previsto']);
    }

    public function testBloqueiaProgramacaoComSaldoProjetadoInsuficiente(): void
    {
        $repository = $this->createMock(FluxoCaixaRepositoryInterface::class);
        $repository->method('obterSaldoAtual')->willReturn(100.00);
        $repository->method('obterEntradasPrevistasMes')->willReturn(0.00);
        $repository->method('obterSaidasPrevistasMes')->willReturn(3100.00);

        $service = new FluxoCaixaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Programacao financeira bloqueada');
        $service->assertPodeProgramarPagamento(1, 10, 200.00, '2026-02-20', '2026-02-18');
    }

    public function testPermiteProgramacaoComSaldoProjetadoSuficiente(): void
    {
        $repository = $this->createMock(FluxoCaixaRepositoryInterface::class);
        $repository->method('obterSaldoAtual')->willReturn(1000.00);
        $repository->method('obterEntradasPrevistasMes')->willReturn(3100.00);
        $repository->method('obterSaidasPrevistasMes')->willReturn(0.00);

        $service = new FluxoCaixaService($repository);
        $service->assertPodeProgramarPagamento(1, 10, 200.00, '2026-02-20', '2026-02-18');

        $this->assertTrue(true);
    }
}

