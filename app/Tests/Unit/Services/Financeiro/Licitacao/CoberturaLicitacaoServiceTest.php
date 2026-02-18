<?php

namespace App\Tests\Unit\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Licitacao\CoberturaLicitacaoService;
use PHPUnit\Framework\TestCase;

class CoberturaLicitacaoServiceTest extends TestCase
{
    public function testGeraResumoComTotaisEPendencias(): void
    {
        $yaml = <<<YAML
itens:
  - item_tr: "2"
    sistema: "Orcamentario - PPA, LDO e LOA"
    status: "atingido"
    evidencia_principal: "docs/sprint7_evidencias_tecnicas.md"
  - item_tr: "3"
    sistema: "Tesouraria"
    status: "parcial"
    evidencia_principal: ""
  - item_tr: "4"
    sistema: "Prestacao de Contas"
    status: "pendente"
    evidencia_principal: ""
YAML;

        $arquivo = sys_get_temp_dir() . '/cobertura-licitacao-' . uniqid('', true) . '.yml';
        file_put_contents($arquivo, $yaml);

        $service = new CoberturaLicitacaoService();
        $resumo = $service->gerarResumo($arquivo);

        $this->assertSame(3, $resumo['total_itens']);
        $this->assertSame(1, $resumo['totais']['atingido']);
        $this->assertSame(1, $resumo['totais']['parcial']);
        $this->assertSame(1, $resumo['totais']['pendente']);
        $this->assertSame(2, count($resumo['pendencias']));

        @unlink($arquivo);
    }
}
