<?php

namespace App\Tests\Unit\Services\Financeiro\Retencoes;

use App\Services\Financeiro\Retencoes\CalculadoraRetencoesTributariasService;
use LogicException;
use PHPUnit\Framework\TestCase;

class CalculadoraRetencoesTributariasServiceTest extends TestCase
{
    public function testCalculaRetencoesComAliquotasPadrao(): void
    {
        $service = new CalculadoraRetencoesTributariasService();
        $resultado = $service->calcular(1000.00);

        $this->assertSame(145.0, $resultado['total_retencoes']);
        $this->assertSame(855.0, $resultado['valor_liquido']);
        $this->assertSame(15.0, $resultado['retencoes']['irrf']['valor']);
        $this->assertSame(20.0, $resultado['retencoes']['iss']['valor']);
        $this->assertSame(110.0, $resultado['retencoes']['inss']['valor']);
    }

    public function testCalculaRetencoesComAliquotasCustomizadasEAdicionais(): void
    {
        $service = new CalculadoraRetencoesTributariasService();
        $resultado = $service->calcular(
            2000.00,
            ['irrf' => 1.00, 'iss' => 3.00, 'inss' => 8.00],
            ['pis' => 13.00]
        );

        $this->assertSame(253.0, $resultado['total_retencoes']);
        $this->assertSame(1747.0, $resultado['valor_liquido']);
        $this->assertSame(13.0, $resultado['retencoes']['pis']['valor']);
    }

    public function testLancaExcecaoQuandoValorBrutoInvalido(): void
    {
        $service = new CalculadoraRetencoesTributariasService();

        $this->expectException(LogicException::class);
        $service->calcular(0.0);
    }
}

