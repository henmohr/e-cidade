<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\CoberturaModulosFinanceirosService;
use PHPUnit\Framework\TestCase;

class CoberturaModulosFinanceirosServiceTest extends TestCase
{
    public function testGeraResumoComStatusPendenteQuandoCoberturaFicaAbaixoDe70(): void
    {
        $diretorio = sys_get_temp_dir() . '/ecidade-modulos-' . uniqid('', true);
        mkdir($diretorio, 0777, true);

        $arquivoOk = $diretorio . '/ok.txt';
        file_put_contents($arquivoOk, 'ok');

        $modulos = [
            [
                'id' => 'MTEST',
                'nome' => 'Modulo Teste',
                'checks' => [
                    ['descricao' => 'arquivo existente', 'tipo' => 'arquivo', 'arquivo' => $arquivoOk],
                    ['descricao' => 'arquivo ausente', 'tipo' => 'arquivo', 'arquivo' => $diretorio . '/nao-existe.txt'],
                ],
            ],
        ];

        $service = new CoberturaModulosFinanceirosService();
        $resumo = $service->gerarResumo($modulos);

        $this->assertSame(1, $resumo['total_modulos']);
        $this->assertSame(1, $resumo['totais']['pendente']);
        $this->assertSame('pendente', $resumo['modulos'][0]['status']);
        $this->assertSame(1, $resumo['modulos'][0]['checks_faltantes']);

        @unlink($arquivoOk);
        @rmdir($diretorio);
    }
}
