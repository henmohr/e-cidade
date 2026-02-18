<?php

namespace App\Tests\Unit\Services\Financeiro\Integracao;

use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepositoryInterface;
use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IntegracaoGovernamentalStatusServiceTest extends TestCase
{
    public function testRegistraPendenciaComStatusInicialCorreto(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('criarRegistro')
            ->with('SICONFI', '2026-02', IntegracaoGovernamentalStatusService::STATUS_PENDENTE, ['lote' => 10])
            ->willReturn(123);

        $service = new IntegracaoGovernamentalStatusService($repository);
        $resultado = $service->registrarPendencia('SICONFI', '2026-02', ['lote' => 10]);

        $this->assertSame(123, $resultado['codigo']);
        $this->assertSame(IntegracaoGovernamentalStatusService::STATUS_PENDENTE, $resultado['status']);
    }

    public function testBloqueiaTransicaoInvalidaDeAceitoParaEnviado(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);
        $service = new IntegracaoGovernamentalStatusService($repository);

        $this->expectException(InvalidArgumentException::class);
        $service->atualizarStatus(
            10,
            IntegracaoGovernamentalStatusService::STATUS_ACEITO,
            IntegracaoGovernamentalStatusService::STATUS_ENVIADO
        );
    }

    public function testReprocessaFalhasMarcandoComoPendente(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('buscarPorStatus')
            ->with([IntegracaoGovernamentalStatusService::STATUS_REJEITADO], 'SICONFI', 20)
            ->willReturn([
                ['codigo' => 7, 'status' => IntegracaoGovernamentalStatusService::STATUS_REJEITADO],
                ['codigo' => 8, 'status' => IntegracaoGovernamentalStatusService::STATUS_REJEITADO],
            ]);

        $repository->expects($this->exactly(2))
            ->method('incrementarTentativaReprocessamento');

        $repository->expects($this->exactly(2))
            ->method('atualizarRegistro')
            ->with(
                $this->logicalOr($this->equalTo(7), $this->equalTo(8)),
                IntegracaoGovernamentalStatusService::STATUS_PENDENTE,
                null,
                'Registro marcado para reprocessamento automatico'
            );

        $service = new IntegracaoGovernamentalStatusService($repository);
        $resultado = $service->reprocessarFalhas('SICONFI', 20);

        $this->assertSame(2, $resultado['total_identificados']);
        $this->assertSame(2, $resultado['total_reprocessados']);
    }
}
