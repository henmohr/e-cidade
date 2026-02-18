<?php

namespace App\Tests\Unit\Services\Financeiro\Integracao;

use App\Services\Financeiro\Integracao\HomologacaoAnexosService;
use PHPUnit\Framework\TestCase;

class HomologacaoAnexosServiceTest extends TestCase
{
    public function testValidaDiretorioComTodosAnexosObrigatorios(): void
    {
        $service = new HomologacaoAnexosService();
        $diretorio = sys_get_temp_dir() . '/ecidade-anexos-ok-' . uniqid('', true);
        mkdir($diretorio, 0777, true);

        foreach ($service->listarAnexosObrigatorios() as $arquivo) {
            file_put_contents($diretorio . DIRECTORY_SEPARATOR . $arquivo, 'conteudo assinado');
        }

        $resultado = $service->validarDiretorio($diretorio);

        $this->assertSame('ok', $resultado['status']);
        $this->assertCount(0, $resultado['ausentes']);
        $this->assertCount(0, $resultado['vazios']);

        foreach ($service->listarAnexosObrigatorios() as $arquivo) {
            @unlink($diretorio . DIRECTORY_SEPARATOR . $arquivo);
        }
        @rmdir($diretorio);
    }

    public function testValidaDiretorioComPendencias(): void
    {
        $service = new HomologacaoAnexosService();
        $diretorio = sys_get_temp_dir() . '/ecidade-anexos-pendente-' . uniqid('', true);
        mkdir($diretorio, 0777, true);

        $arquivos = $service->listarAnexosObrigatorios();
        file_put_contents($diretorio . DIRECTORY_SEPARATOR . $arquivos[0], 'conteudo assinado');

        $resultado = $service->validarDiretorio($diretorio);

        $this->assertSame('pendente', $resultado['status']);
        $this->assertGreaterThan(0, count($resultado['ausentes']));

        @unlink($diretorio . DIRECTORY_SEPARATOR . $arquivos[0]);
        @rmdir($diretorio);
    }
}
