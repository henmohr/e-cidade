<?php

namespace App\Tests\Unit\Services\Financeiro\Integracao;

use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepositoryInterface;
use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IntegracaoGovernamentalHomologacaoServiceTest extends TestCase
{
    public function testRegistraResultadoHomologacaoComProtocolo(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);

        $repository->expects($this->once())
            ->method('buscarPorCodigo')
            ->with(100)
            ->willReturn([
                'codigo' => 100,
                'sistema' => 'SICONFI',
                'status' => IntegracaoGovernamentalStatusService::STATUS_ENVIADO,
            ]);

        $repository->expects($this->once())
            ->method('atualizarRegistro')
            ->with(
                100,
                IntegracaoGovernamentalStatusService::STATUS_ACEITO,
                'PROTOCOLO-2026-0001',
                'homologado'
            );

        $service = new IntegracaoGovernamentalStatusService($repository);
        $resultado = $service->registrarResultadoHomologacao(
            100,
            IntegracaoGovernamentalStatusService::STATUS_ACEITO,
            'PROTOCOLO-2026-0001',
            'homologado'
        );

        $this->assertSame('SICONFI', $resultado['sistema']);
        $this->assertSame('aceito', $resultado['status_novo']);
    }

    public function testFalhaQuandoRegistroNaoExiste(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);
        $repository->method('buscarPorCodigo')->willReturn(null);

        $service = new IntegracaoGovernamentalStatusService($repository);

        $this->expectException(InvalidArgumentException::class);
        $service->registrarResultadoHomologacao(999, 'aceito', 'X');
    }

    public function testGeraResumoHomologacaoPorStatus(): void
    {
        $repository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);

        $repository->expects($this->exactly(4))
            ->method('buscarPorStatus')
            ->willReturnOnConsecutiveCalls(
                [['codigo' => 1]],
                [['codigo' => 2], ['codigo' => 3]],
                [['codigo' => 4]],
                []
            );

        $service = new IntegracaoGovernamentalStatusService($repository);
        $resumo = $service->gerarResumoHomologacao('SICONFI');

        $this->assertSame(1, $resumo['totais']['pendente']);
        $this->assertSame(2, $resumo['totais']['enviado']);
        $this->assertSame(1, $resumo['totais']['aceito']);
        $this->assertSame(0, $resumo['totais']['rejeitado']);
    }
}
