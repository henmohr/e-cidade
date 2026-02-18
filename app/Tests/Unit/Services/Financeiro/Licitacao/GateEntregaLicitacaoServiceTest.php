<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\GateEntregaLicitacaoService;
use PHPUnit\Framework\TestCase;

class GateEntregaLicitacaoServiceTest extends TestCase
{
    public function testRetornaAptoQuandoTodosChecksPassam(): void
    {
        $dir = sys_get_temp_dir() . '/ecidade-gate-' . uniqid('', true);
        mkdir($dir, 0777, true);

        file_put_contents($dir . '/s11.json', json_encode(['percentual_global' => 100]));
        file_put_contents($dir . '/s12.json', json_encode(['status_recomendado' => 'pronto_para_homologacao']));
        file_put_contents($dir . '/s14.json', json_encode(['status_final' => 'apto_para_banca']));
        file_put_contents($dir . '/banca.json', json_encode(['status_final' => 'apto_para_banca']));

        $service = new GateEntregaLicitacaoService();
        $resumo = $service->gerarResumo([
            'sprint11' => $dir . '/s11.json',
            'sprint12' => $dir . '/s12.json',
            'sprint14' => $dir . '/s14.json',
            'banca' => $dir . '/banca.json',
        ]);

        $this->assertSame('apto_para_entrega', $resumo['status_final']);
        $this->assertSame(0, count($resumo['pendencias']));

        @unlink($dir . '/s11.json');
        @unlink($dir . '/s12.json');
        @unlink($dir . '/s14.json');
        @unlink($dir . '/banca.json');
        @rmdir($dir);
    }

    public function testRetornaBloqueadoQuandoArquivoEstaAusente(): void
    {
        $service = new GateEntregaLicitacaoService();
        $resumo = $service->gerarResumo([
            'sprint11' => '/tmp/nao-existe-s11.json',
            'sprint12' => '/tmp/nao-existe-s12.json',
            'sprint14' => '/tmp/nao-existe-s14.json',
            'banca' => '/tmp/nao-existe-banca.json',
        ]);

        $this->assertSame('bloqueado', $resumo['status_final']);
        $this->assertGreaterThan(0, count($resumo['pendencias']));
    }
}
