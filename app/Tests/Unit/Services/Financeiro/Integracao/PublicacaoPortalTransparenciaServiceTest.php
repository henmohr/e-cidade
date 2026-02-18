<?php

namespace App\Tests\Unit\Services\Financeiro\Integracao;

use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepositoryInterface;
use App\Repositories\Financeiro\Integracao\PortalTransparenciaPublicacaoRepositoryInterface;
use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use App\Services\Financeiro\Integracao\PublicacaoPortalTransparenciaService;
use PHPUnit\Framework\TestCase;

class PublicacaoPortalTransparenciaServiceTest extends TestCase
{
    public function testPublicaConsolidadoComResumoFinanceiro(): void
    {
        $publicacaoRepository = $this->createMock(PortalTransparenciaPublicacaoRepositoryInterface::class);
        $integracaoRepository = $this->createMock(IntegracaoGovernamentalRepositoryInterface::class);

        $publicacaoRepository->method('obterDadosReceitas')->willReturn([
            ['valor_total' => 1200.50, 'total_registros' => 3],
        ]);
        $publicacaoRepository->method('obterDadosDespesas')->willReturn([
            ['valor_total' => 900.25, 'total_registros' => 2],
        ]);
        $publicacaoRepository->method('obterDadosContratos')->willReturn([
            ['valor_total' => 500.00, 'total_registros' => 1],
        ]);

        $integracaoRepository->expects($this->once())
            ->method('criarRegistro')
            ->with(
                'PORTAL_TRANSPARENCIA',
                '2026-02-18',
                IntegracaoGovernamentalStatusService::STATUS_ACEITO,
                $this->isType('array')
            )
            ->willReturn(321);

        $service = new PublicacaoPortalTransparenciaService($publicacaoRepository, $integracaoRepository);
        $resultado = $service->publicar('2026-02-18');

        $this->assertSame(321, $resultado['codigo_publicacao']);
        $this->assertSame(1200.5, $resultado['resumo']['receitas']['valor_total']);
        $this->assertSame(900.25, $resultado['resumo']['despesas']['valor_total']);
        $this->assertSame(500.0, $resultado['resumo']['contratos']['valor_total']);
    }
}
