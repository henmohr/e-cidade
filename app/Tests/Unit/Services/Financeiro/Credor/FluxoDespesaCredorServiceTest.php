<?php

namespace App\Tests\Unit\Services\Financeiro\Credor;

use App\Repositories\Financeiro\Credor\FluxoDespesaCredorRepositoryInterface;
use App\Services\Financeiro\Credor\FluxoDespesaCredorService;
use App\Services\Financeiro\Credor\ValidacaoCredorService;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use PHPUnit\Framework\TestCase;

class FluxoDespesaCredorServiceTest extends TestCase
{
    public function testRetornaCredorPendenteQuandoValidacaoDoCredorFalha(): void
    {
        $repository = $this->createMock(FluxoDespesaCredorRepositoryInterface::class);

        $validacaoCredor = $this->createMock(ValidacaoCredorService::class);
        $validacaoCredor->method('validarPorCgm')->willReturn([
            'apto' => false,
            'pendencias' => ['CPF/CNPJ invalido.'],
        ]);

        $cicloService = $this->createMock(CicloDespesaService::class);

        $service = new FluxoDespesaCredorService($repository, $validacaoCredor, $cicloService);
        $resultado = $service->validarFluxoMinimo(10, 100);

        $this->assertSame('CREDOR_PENDENTE', $resultado['status']);
        $this->assertFalse($resultado['etapas']['credor']);
    }

    public function testRetornaPendenteEmpenhoQuandoEmpenhoNaoPertenceAoCredor(): void
    {
        $repository = $this->createMock(FluxoDespesaCredorRepositoryInterface::class);
        $repository->method('hasEmpenhoDoCredor')->willReturn(false);

        $validacaoCredor = $this->createMock(ValidacaoCredorService::class);
        $validacaoCredor->method('validarPorCgm')->willReturn([
            'apto' => true,
            'pendencias' => [],
        ]);

        $cicloService = $this->createMock(CicloDespesaService::class);

        $service = new FluxoDespesaCredorService($repository, $validacaoCredor, $cicloService);
        $resultado = $service->validarFluxoMinimo(10, 100);

        $this->assertSame('PENDENTE_EMPENHO', $resultado['status']);
        $this->assertFalse($resultado['etapas']['empenho']);
    }

    public function testRetornaPendenteAtestoQuandoNaoHaAtesto(): void
    {
        $repository = $this->createMock(FluxoDespesaCredorRepositoryInterface::class);
        $repository->method('hasEmpenhoDoCredor')->willReturn(true);
        $repository->method('hasAtestoDoEmpenho')->willReturn(false);

        $validacaoCredor = $this->createMock(ValidacaoCredorService::class);
        $validacaoCredor->method('validarPorCgm')->willReturn([
            'apto' => true,
            'pendencias' => [],
        ]);

        $cicloService = $this->createMock(CicloDespesaService::class);
        $cicloService->expects($this->once())->method('assertPodeLiquidar');

        $service = new FluxoDespesaCredorService($repository, $validacaoCredor, $cicloService);
        $resultado = $service->validarFluxoMinimo(10, 100);

        $this->assertSame('PENDENTE_ATESTO', $resultado['status']);
        $this->assertFalse($resultado['etapas']['atesto']);
    }

    public function testRetornaFluxoCompletoQuandoTodasEtapasOk(): void
    {
        $repository = $this->createMock(FluxoDespesaCredorRepositoryInterface::class);
        $repository->method('hasEmpenhoDoCredor')->willReturn(true);
        $repository->method('hasAtestoDoEmpenho')->willReturn(true);
        $repository->method('hasPagamentoDoEmpenho')->willReturn(true);

        $validacaoCredor = $this->createMock(ValidacaoCredorService::class);
        $validacaoCredor->method('validarPorCgm')->willReturn([
            'apto' => true,
            'pendencias' => [],
        ]);

        $cicloService = $this->createMock(CicloDespesaService::class);
        $cicloService->expects($this->once())->method('assertPodeLiquidar');
        $cicloService->expects($this->once())->method('assertPodePagar');

        $service = new FluxoDespesaCredorService($repository, $validacaoCredor, $cicloService);
        $resultado = $service->validarFluxoMinimo(10, 100);

        $this->assertSame('FLUXO_COMPLETO', $resultado['status']);
        $this->assertTrue($resultado['etapas']['pagamento']);
    }
}

