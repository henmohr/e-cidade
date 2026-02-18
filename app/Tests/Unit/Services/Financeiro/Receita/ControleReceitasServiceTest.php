<?php

namespace App\Tests\Unit\Services\Financeiro\Receita;

use App\Repositories\Financeiro\Receita\ControleReceitasRepositoryInterface;
use App\Services\Financeiro\Receita\ControleReceitasService;
use PHPUnit\Framework\TestCase;

class ControleReceitasServiceTest extends TestCase
{
    public function testConsolidaReceitasTributariasETransferencias(): void
    {
        $repository = $this->createMock(ControleReceitasRepositoryInterface::class);
        $repository->method('obterReceitasConsolidadas')->willReturn([
            [
                'codigo_receita' => '1111',
                'descricao_curta' => 'IPTU',
                'descricao_completa' => 'IMPOSTO PREDIAL E TERRITORIAL URBANO',
                'valor_total' => 1000.00,
            ],
            [
                'codigo_receita' => '1711',
                'descricao_curta' => 'FPM',
                'descricao_completa' => 'TRANSFERENCIA FPM',
                'valor_total' => 2000.00,
            ],
            [
                'codigo_receita' => '9999',
                'descricao_curta' => 'OUTRAS',
                'descricao_completa' => 'OUTRAS RECEITAS',
                'valor_total' => 300.00,
            ],
        ]);

        $service = new ControleReceitasService($repository);
        $resultado = $service->consolidar('2026-01-01', '2026-01-31');

        $this->assertSame(1000.0, $resultado['totais']['tributarias']);
        $this->assertSame(2000.0, $resultado['totais']['transferencias_intergovernamentais']);
        $this->assertSame(300.0, $resultado['totais']['demais_receitas']);
        $this->assertCount(1, $resultado['tributarias']);
        $this->assertCount(1, $resultado['transferencias_intergovernamentais']);
    }
}

