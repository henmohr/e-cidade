<?php

namespace App\Tests\Unit\Services\Financeiro\Receita;

use App\Repositories\Financeiro\Receita\ClassificacaoReceitaRepositoryInterface;
use App\Services\Financeiro\Receita\ClassificacaoReceitaService;
use PHPUnit\Framework\TestCase;

class ClassificacaoReceitaServiceTest extends TestCase
{
    public function testClassificaReceitaCorrente(): void
    {
        $repository = $this->createMock(ClassificacaoReceitaRepositoryInterface::class);
        $repository->method('obterReceitaPorCodigo')->willReturn([
            'k02_codigo' => 123456,
            'k02_descr' => 'Receita corrente teste',
            'k02_drecei' => 'Receita corrente teste',
        ]);

        $service = new ClassificacaoReceitaService($repository);
        $resultado = $service->classificarPorCodigo(123456);

        $this->assertSame('CLASSIFICADA', $resultado['status']);
        $this->assertSame('RECEITAS_CORRENTES', $resultado['grupo']);
    }

    public function testClassificaReceitaCapitalIntraorcamentaria(): void
    {
        $repository = $this->createMock(ClassificacaoReceitaRepositoryInterface::class);
        $repository->method('obterReceitaPorCodigo')->willReturn([
            'k02_codigo' => 823456,
            'k02_descr' => 'Receita capital intra',
            'k02_drecei' => 'Receita capital intra',
        ]);

        $service = new ClassificacaoReceitaService($repository);
        $resultado = $service->classificarPorCodigo(823456);

        $this->assertSame('CLASSIFICADA', $resultado['status']);
        $this->assertSame('RECEITAS_DE_CAPITAL', $resultado['grupo']);
        $this->assertSame('INTRAORCAMENTARIA', $resultado['subgrupo']);
    }

    public function testRetornaPendenteQuandoNaturezaNaoMapeada(): void
    {
        $repository = $this->createMock(ClassificacaoReceitaRepositoryInterface::class);
        $repository->method('obterReceitaPorCodigo')->willReturn([
            'k02_codigo' => 923456,
            'k02_descr' => 'Receita nao mapeada',
            'k02_drecei' => 'Receita nao mapeada',
        ]);

        $service = new ClassificacaoReceitaService($repository);
        $resultado = $service->classificarPorCodigo(923456);

        $this->assertSame('PENDENTE_CLASSIFICACAO', $resultado['status']);
        $this->assertNull($resultado['grupo']);
    }
}

