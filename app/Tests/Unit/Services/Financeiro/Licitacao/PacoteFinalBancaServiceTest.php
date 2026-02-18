<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Integracao\HomologacaoAnexosService;
use App\Services\Financeiro\Licitacao\CoberturaLicitacaoService;
use App\Services\Financeiro\Licitacao\PacoteFinalBancaService;
use PHPUnit\Framework\TestCase;

class PacoteFinalBancaServiceTest extends TestCase
{
    public function testGeraPacoteComStatusAptoQuandoTudoValido(): void
    {
        $cobertura = $this->createMock(CoberturaLicitacaoService::class);
        $anexos = $this->createMock(HomologacaoAnexosService::class);

        $cobertura->method('gerarResumo')->willReturn([
            'total_itens' => 8,
            'percentual_atingido' => 100.0,
            'status_recomendado' => 'apto_para_banca',
        ]);

        $anexos->method('validarDiretorio')->willReturn([
            'status' => 'ok',
            'ausentes' => [],
            'vazios' => [],
        ]);

        $dir = sys_get_temp_dir() . '/ecidade-pacote-final-' . uniqid('', true);
        mkdir($dir, 0777, true);

        $doc = $dir . '/doc.md';
        file_put_contents($doc, 'ok');

        $service = new PacoteFinalBancaService($cobertura, $anexos);
        $resultado = $service->gerar('fake.yml', 'anexos', $dir, [$doc]);

        $this->assertSame('apto_para_banca', $resultado['status_final']);
        $this->assertFileExists($resultado['manifesto']);
        $this->assertFileExists($resultado['resumo']);

        @unlink($resultado['manifesto']);
        @unlink($resultado['resumo']);
        @unlink($doc);
        @rmdir($dir);
    }
}
