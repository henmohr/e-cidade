<?php

namespace App\Tests\Unit\Services\Financeiro\Integracao;

use App\Services\Financeiro\Integracao\ChecklistHomologacaoExternaService;
use App\Services\Financeiro\Integracao\HomologacaoAnexosService;
use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use PHPUnit\Framework\TestCase;

class ChecklistHomologacaoExternaServiceTest extends TestCase
{
    public function testGeraResumoAptoParaBancaQuandoTodosSistemasPassam(): void
    {
        $statusService = $this->createMock(IntegracaoGovernamentalStatusService::class);
        $anexosService = $this->createMock(HomologacaoAnexosService::class);

        $statusService->expects($this->exactly(3))
            ->method('gerarResumoHomologacao')
            ->willReturn([
                'totais' => [
                    'pendente' => 0,
                    'enviado' => 0,
                    'aceito' => 1,
                    'rejeitado' => 0,
                ],
            ]);

        $anexosService->expects($this->once())
            ->method('validarDiretorio')
            ->willReturn([
                'status' => 'ok',
                'ausentes' => [],
                'vazios' => [],
            ]);

        $service = new ChecklistHomologacaoExternaService($statusService, $anexosService);
        $resumo = $service->gerarResumo(['SICONFI', 'TCE_PR', 'PORTAL_TRANSPARENCIA'], 'docs/anexos_homologacao_assinados');

        $this->assertSame(3, $resumo['totais']['apto']);
        $this->assertSame(0, $resumo['totais']['bloqueado']);
        $this->assertSame('apto_para_banca', $resumo['status_final']);
    }

    public function testMarcaSistemaComoBloqueadoQuandoExisteRejeitado(): void
    {
        $statusService = $this->createMock(IntegracaoGovernamentalStatusService::class);
        $anexosService = $this->createMock(HomologacaoAnexosService::class);

        $statusService->expects($this->once())
            ->method('gerarResumoHomologacao')
            ->willReturn([
                'totais' => [
                    'pendente' => 0,
                    'enviado' => 0,
                    'aceito' => 1,
                    'rejeitado' => 1,
                ],
            ]);

        $anexosService->method('validarDiretorio')->willReturn([
            'status' => 'ok',
            'ausentes' => [],
            'vazios' => [],
        ]);

        $service = new ChecklistHomologacaoExternaService($statusService, $anexosService);
        $resumo = $service->gerarResumo(['SICONFI'], 'docs/anexos_homologacao_assinados');

        $this->assertSame('bloqueado', $resumo['sistemas'][0]['status']);
        $this->assertSame('plano_de_acao_obrigatorio', $resumo['status_final']);
    }

    public function testModoOfflineComArquivoProtocolosGeraAptoParaBanca(): void
    {
        $statusService = $this->createMock(IntegracaoGovernamentalStatusService::class);
        $anexosService = $this->createMock(HomologacaoAnexosService::class);

        $statusService->expects($this->never())
            ->method('gerarResumoHomologacao');

        $anexosService->expects($this->once())
            ->method('validarDiretorio')
            ->willReturn([
                'status' => 'ok',
                'ausentes' => [],
                'vazios' => [],
            ]);

        $arquivo = sys_get_temp_dir() . '/protocolos-' . uniqid('', true) . '.yml';
        file_put_contents($arquivo, <<<YAML
sistemas:
  SICONFI:
    pendente: 0
    enviado: 0
    aceito: 1
    rejeitado: 0
YAML);

        $service = new ChecklistHomologacaoExternaService($statusService, $anexosService);
        $resumo = $service->gerarResumo(
            ['SICONFI'],
            'docs/anexos_homologacao_assinados',
            200,
            $arquivo,
            true
        );

        $this->assertSame('apto', $resumo['sistemas'][0]['status']);
        $this->assertSame('arquivo_protocolos', $resumo['sistemas'][0]['fonte']);
        $this->assertSame('apto_para_banca', $resumo['status_final']);

        @unlink($arquivo);
    }
}
