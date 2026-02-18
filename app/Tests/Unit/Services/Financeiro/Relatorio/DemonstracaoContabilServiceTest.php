<?php

namespace App\Tests\Unit\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\DemonstracaoRepositoryInterface;
use App\Services\Financeiro\Relatorio\DemonstracaoContabilService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DemonstracaoContabilServiceTest extends TestCase
{
    public function testGeraDvpEDfc(): void
    {
        $repository = $this->createMock(DemonstracaoRepositoryInterface::class);
        $repository->method('obterDadosDvp')->willReturn([
            'variacoes_ativas' => 1000.0,
            'variacoes_passivas' => 700.0,
            'resultado_patrimonial' => 300.0,
        ]);
        $repository->method('obterDadosDfc')->willReturn([
            'entradas_operacionais' => 900.0,
            'saidas_operacionais' => 650.0,
            'variacao_liquida_caixa' => 250.0,
        ]);

        $service = new DemonstracaoContabilService($repository);
        $resultado = $service->gerar(DemonstracaoContabilService::TIPO_TODAS, '2026-01-01', '2026-01-31');

        $this->assertSame('todas', $resultado['tipo']);
        $this->assertArrayHasKey('dvp', $resultado['demonstracoes']);
        $this->assertArrayHasKey('dfc', $resultado['demonstracoes']);
        $this->assertSame(250.0, $resultado['demonstracoes']['dfc']['variacao_liquida_caixa']);
    }

    public function testGeraSomenteDvp(): void
    {
        $repository = $this->createMock(DemonstracaoRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('obterDadosDvp')
            ->willReturn([
                'variacoes_ativas' => 500.0,
                'variacoes_passivas' => 200.0,
                'resultado_patrimonial' => 300.0,
            ]);

        $repository->expects($this->never())->method('obterDadosDfc');

        $service = new DemonstracaoContabilService($repository);
        $resultado = $service->gerar(DemonstracaoContabilService::TIPO_DVP);

        $this->assertCount(1, $resultado['demonstracoes']);
        $this->assertArrayHasKey('dvp', $resultado['demonstracoes']);
    }

    public function testLancaErroParaTipoInvalido(): void
    {
        $repository = $this->createMock(DemonstracaoRepositoryInterface::class);
        $service = new DemonstracaoContabilService($repository);

        $this->expectException(InvalidArgumentException::class);
        $service->gerar('nao-suportado');
    }
}
