<?php

namespace App\Tests\Unit\Support\Educacao;

use App\Support\Educacao\TransporteEscolarDashboard;
use App\Tests\TestCase;

class TransporteEscolarDashboardTest extends TestCase
{
    public function testPayloadContemRequisitosEAcoesDoModulo(): void
    {
        $dashboard = new TransporteEscolarDashboard();

        $payload = $dashboard->payload();

        $this->assertCount(8, $payload['requisitos']);
        $this->assertSame('A1', $payload['requisitos'][0]['codigo']);
        $this->assertNotEmpty($payload['linhas']);
        $this->assertNotEmpty($payload['veiculos']);
        $this->assertNotEmpty($payload['legado']);
        $this->assertContains('Exportacao SETE em formato estruturado.', $payload['relatorios']);
    }
}
