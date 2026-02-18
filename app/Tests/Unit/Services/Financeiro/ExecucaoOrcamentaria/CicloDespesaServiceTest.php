<?php

namespace App\Tests\Unit\Services\Financeiro\ExecucaoOrcamentaria;

use App\Repositories\Financeiro\ExecucaoOrcamentaria\CicloDespesaRepositoryInterface;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use LogicException;
use PHPUnit\Framework\TestCase;

class CicloDespesaServiceTest extends TestCase
{
    public function testBloqueiaEmpenhoSemFixacao(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasFixacao')->willReturn(false);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao e permitido empenhar sem fixacao valida');
        $service->assertPodeEmpenhar(2026, 123);
    }

    public function testBloqueiaEmpenhoSemSaldoDisponivelSuficiente(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasFixacao')->willReturn(true);
        $repository->method('getSaldoDisponivelDotacao')->willReturn(100.0);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao e permitido empenhar valor acima do saldo disponivel da dotacao');
        $service->assertPodeEmpenhar(2026, 123, 150.0);
    }

    public function testPermiteEmpenhoQuandoSaldoDisponivelEValido(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasFixacao')->willReturn(true);
        $repository->method('getSaldoDisponivelDotacao')->willReturn(300.0);

        $service = new CicloDespesaService($repository);
        $service->assertPodeEmpenhar(2026, 123, 150.0);

        $this->assertTrue(true);
    }

    public function testBloqueiaLiquidacaoSemEmpenho(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(false);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao e permitido liquidar sem empenho correspondente');
        $service->assertPodeLiquidar(456);
    }

    public function testBloqueiaPagamentoSemLiquidacao(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(true);
        $repository->method('hasLiquidacao')->willReturn(false);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao e permitido pagar sem liquidacao valida');
        $service->assertPodePagar(789);
    }

    public function testPermitePagamentoQuandoCicloValido(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(true);
        $repository->method('hasLiquidacao')->willReturn(true);

        $service = new CicloDespesaService($repository);
        $service->assertPodePagar(42);

        $this->assertTrue(true);
    }

    public function testObterSituacaoRefleteEtapasDoCiclo(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasLiquidacao')->willReturn(true);
        $repository->method('hasPagamento')->willReturn(false);

        $service = new CicloDespesaService($repository);
        $situacao = $service->obterSituacao(10);

        $this->assertTrue($situacao['empenho']);
        $this->assertTrue($situacao['liquidacao']);
        $this->assertFalse($situacao['pagamento']);
    }

    public function testBloqueiaPagamentoQuandoOrdemNaoPossuiEmpenhoResolvido(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('getNumeroEmpenhoPorOrdemPagamento')->willReturn(null);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao foi possivel identificar o empenho da ordem de pagamento informada');
        $service->assertPodeRegistrarPagamentoPorOrdem(1001);
    }

    public function testValidaPagamentoPorOrdemQuandoEmpenhoResolvido(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('getNumeroEmpenhoPorOrdemPagamento')->willReturn(1002);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(true);
        $repository->method('hasLiquidacao')->willReturn(true);

        $service = new CicloDespesaService($repository);
        $service->assertPodeRegistrarPagamentoPorOrdem(555);

        $this->assertTrue(true);
    }

    public function testBloqueiaLiquidacaoSemFixacaoDoEmpenho(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(false);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Nao e permitido liquidar sem fixacao valida da dotacao do empenho');
        $service->assertPodeLiquidar(999);
    }

    public function testBloqueiaSequenciaQuandoHaEmpenhoSemFixacao(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(false);
        $repository->method('hasLiquidacao')->willReturn(false);
        $repository->method('hasPagamento')->willReturn(false);

        $service = new CicloDespesaService($repository);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Sequencia invalida: existe empenho sem fixacao valida');
        $service->assertSequenciaObrigatoria(111);
    }

    public function testPermiteSequenciaQuandoEstadoConsistente(): void
    {
        $repository = $this->createMock(CicloDespesaRepositoryInterface::class);
        $repository->method('hasEmpenho')->willReturn(true);
        $repository->method('hasFixacaoParaEmpenho')->willReturn(true);
        $repository->method('hasLiquidacao')->willReturn(true);
        $repository->method('hasPagamento')->willReturn(true);

        $service = new CicloDespesaService($repository);
        $service->assertSequenciaObrigatoria(111);

        $this->assertTrue(true);
    }
}
