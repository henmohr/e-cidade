<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\RastreabilidadeFuncionalService;
use PHPUnit\Framework\TestCase;

class RastreabilidadeFuncionalServiceTest extends TestCase
{
    public function testGeraResumoComStatusParcialQuandoFaltaEvidencia(): void
    {
        $dir = sys_get_temp_dir() . '/ecidade-rastreabilidade-' . uniqid('', true);
        mkdir($dir, 0777, true);

        $evidenciaOk = $dir . '/ok.md';
        file_put_contents($evidenciaOk, '# ok');

        $cenarios = [
            [
                'id' => 'CX',
                'modulo' => 'Modulo Teste',
                'objetivo' => 'Validar rastreabilidade',
                'pre_condicoes' => ['Pre 1'],
                'passos' => ['Passo 1'],
                'evidencias' => [
                    ['descricao' => 'Ok', 'arquivo' => $evidenciaOk],
                    ['descricao' => 'Faltante', 'arquivo' => $dir . '/faltante.md'],
                ],
            ],
        ];

        $service = new RastreabilidadeFuncionalService();
        $resumo = $service->gerarResumo($cenarios);

        $this->assertSame(1, $resumo['total_cenarios']);
        $this->assertSame(1, $resumo['totais']['parcial']);
        $this->assertSame('homologacao_assistida', $resumo['status_recomendado']);
        $this->assertSame(1, $resumo['cenarios'][0]['evidencias_faltantes']);

        @unlink($evidenciaOk);
        @rmdir($dir);
    }
}
