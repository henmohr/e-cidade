<?php

namespace App\Tests\Unit\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\ConciliacaoBancariaRepositoryInterface;
use App\Services\Financeiro\Tesouraria\ConciliacaoBancariaService;
use PHPUnit\Framework\TestCase;

class ConciliacaoBancariaServiceTest extends TestCase
{
    public function testRetornaPendenteQuandoSaldoSistemaNaoEncontrado(): void
    {
        $repository = $this->createMock(ConciliacaoBancariaRepositoryInterface::class);
        $repository->method('obterSaldoSistema')->willReturn(null);

        $service = new ConciliacaoBancariaService($repository);
        $resultado = $service->conciliar(10, 100, 1200.50, '2026-02-18');

        $this->assertSame('PENDENTE_SEM_SALDO_SISTEMA', $resultado['status']);
        $this->assertFalse($resultado['conciliado']);
    }

    public function testRetornaConciliadoQuandoDiferencaDentroDaTolerancia(): void
    {
        $repository = $this->createMock(ConciliacaoBancariaRepositoryInterface::class);
        $repository->method('obterSaldoSistema')->willReturn(1000.00);

        $service = new ConciliacaoBancariaService($repository);
        $resultado = $service->conciliar(10, 100, 1000.005, '2026-02-18', 0.01);

        $this->assertSame('CONCILIADO', $resultado['status']);
        $this->assertTrue($resultado['conciliado']);
    }

    public function testRetornaPendenteQuandoDiferencaExcedeTolerancia(): void
    {
        $repository = $this->createMock(ConciliacaoBancariaRepositoryInterface::class);
        $repository->method('obterSaldoSistema')->willReturn(1000.00);

        $service = new ConciliacaoBancariaService($repository);
        $resultado = $service->conciliar(10, 100, 1025.00, '2026-02-18', 0.01);

        $this->assertSame('PENDENTE_DIVERGENCIA', $resultado['status']);
        $this->assertFalse($resultado['conciliado']);
        $this->assertSame(25.0, $resultado['diferenca']);
    }
}

