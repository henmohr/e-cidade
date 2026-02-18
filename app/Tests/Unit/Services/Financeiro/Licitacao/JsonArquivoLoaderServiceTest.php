<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\JsonArquivoLoaderService;
use PHPUnit\Framework\TestCase;

class JsonArquivoLoaderServiceTest extends TestCase
{
    public function testCarregarRetornaNuloParaJsonInvalido(): void
    {
        $arquivo = sys_get_temp_dir() . '/ecidade-json-invalido-' . uniqid('', true) . '.json';
        file_put_contents($arquivo, '{invalido}');

        $service = new JsonArquivoLoaderService();
        $dados = $service->carregar($arquivo);

        $this->assertNull($dados);

        @unlink($arquivo);
    }

    public function testCarregarMapaRetornaErroParaArquivosAusentes(): void
    {
        $service = new JsonArquivoLoaderService();
        $resultado = $service->carregarMapa([
            'a' => '/tmp/inexistente-a.json',
            'b' => '/tmp/inexistente-b.json',
        ]);

        $this->assertSame(0, count($resultado['dados']));
        $this->assertSame(2, count($resultado['erros']));
    }
}
