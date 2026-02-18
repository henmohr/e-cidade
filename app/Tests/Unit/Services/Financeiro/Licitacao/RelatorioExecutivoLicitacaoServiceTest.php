<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\RelatorioExecutivoLicitacaoService;
use PHPUnit\Framework\TestCase;

class RelatorioExecutivoLicitacaoServiceTest extends TestCase
{
    public function testRetornaRecomendadoQuandoTudoEstaApto(): void
    {
        $dir = sys_get_temp_dir() . '/ecidade-relatorio-exec-' . uniqid('', true);
        mkdir($dir, 0777, true);

        file_put_contents($dir . '/s11.json', json_encode(['percentual_global' => 100]));
        file_put_contents($dir . '/s12.json', json_encode(['status_recomendado' => 'pronto_para_homologacao']));
        file_put_contents($dir . '/s14.json', json_encode(['status_final' => 'apto_para_banca']));
        file_put_contents($dir . '/s15.json', json_encode(['status_final' => 'apto_para_entrega']));

        $service = new RelatorioExecutivoLicitacaoService();
        $resumo = $service->gerarResumo([
            'sprint11' => $dir . '/s11.json',
            'sprint12' => $dir . '/s12.json',
            'sprint14' => $dir . '/s14.json',
            'sprint15' => $dir . '/s15.json',
        ]);

        $this->assertSame('recomendado_para_protocolo', $resumo['decisao']);
        $this->assertSame('apto_para_entrega', $resumo['status_entrega']);

        @unlink($dir . '/s11.json');
        @unlink($dir . '/s12.json');
        @unlink($dir . '/s14.json');
        @unlink($dir . '/s15.json');
        @rmdir($dir);
    }

    public function testRetornaSegurarEnvioQuandoFaltaArquivo(): void
    {
        $service = new RelatorioExecutivoLicitacaoService();
        $resumo = $service->gerarResumo([
            'sprint11' => '/tmp/inexistente-11.json',
            'sprint12' => '/tmp/inexistente-12.json',
            'sprint14' => '/tmp/inexistente-14.json',
            'sprint15' => '/tmp/inexistente-15.json',
        ]);

        $this->assertSame('segurar_envio', $resumo['decisao']);
        $this->assertGreaterThan(0, count($resumo['pendencias']));
    }
}
