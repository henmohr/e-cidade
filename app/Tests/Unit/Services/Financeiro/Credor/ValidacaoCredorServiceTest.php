<?php

namespace App\Tests\Unit\Services\Financeiro\Credor;

use App\Repositories\Financeiro\Credor\CredorRepositoryInterface;
use App\Services\Financeiro\Credor\ValidacaoCredorService;
use PHPUnit\Framework\TestCase;

class ValidacaoCredorServiceTest extends TestCase
{
    public function testRetornaNaoEncontradoQuandoCgmNaoExiste(): void
    {
        $repository = $this->createMock(CredorRepositoryInterface::class);
        $repository->method('obterCredorPorCgm')->willReturn(null);

        $service = new ValidacaoCredorService($repository);
        $resultado = $service->validarPorCgm(9999);

        $this->assertSame('NAO_ENCONTRADO', $resultado['status']);
        $this->assertFalse($resultado['apto']);
    }

    public function testRetornaPendenteQuandoDocumentoInvalidoEAusenciaFornecedor(): void
    {
        $repository = $this->createMock(CredorRepositoryInterface::class);
        $repository->method('obterCredorPorCgm')->willReturn([
            'z01_numcgm' => 10,
            'z01_nome' => 'Fornecedor Teste',
            'z01_cgccpf' => '12345678900',
            'z01_email' => 'x@x.com',
            'fornecedor_cgm' => null,
            'pc60_bloqueado' => 'f',
            'pc60_inscriestadual' => null,
            'pc60_numeroregistro' => null,
            'pc60_orgaoreg' => null,
        ]);

        $service = new ValidacaoCredorService($repository);
        $resultado = $service->validarPorCgm(10);

        $this->assertSame('PENDENTE_DOCUMENTAL', $resultado['status']);
        $this->assertFalse($resultado['apto']);
        $this->assertNotEmpty($resultado['pendencias']);
    }

    public function testRetornaAptoQuandoPessoaFisicaValidaSemPendencias(): void
    {
        $repository = $this->createMock(CredorRepositoryInterface::class);
        $repository->method('obterCredorPorCgm')->willReturn([
            'z01_numcgm' => 11,
            'z01_nome' => 'Credor PF',
            'z01_cgccpf' => '11144477735',
            'z01_email' => 'credor@teste.com',
            'fornecedor_cgm' => 11,
            'pc60_bloqueado' => 'f',
            'pc60_inscriestadual' => null,
            'pc60_numeroregistro' => null,
            'pc60_orgaoreg' => null,
        ]);

        $service = new ValidacaoCredorService($repository);
        $resultado = $service->validarPorCgm(11);

        $this->assertSame('APTO', $resultado['status']);
        $this->assertTrue($resultado['apto']);
        $this->assertEmpty($resultado['pendencias']);
    }

    public function testRetornaPendenteQuandoPessoaJuridicaSemDocumentacaoObrigatoria(): void
    {
        $repository = $this->createMock(CredorRepositoryInterface::class);
        $repository->method('obterCredorPorCgm')->willReturn([
            'z01_numcgm' => 12,
            'z01_nome' => 'Credor PJ',
            'z01_cgccpf' => '11222333000181',
            'z01_email' => 'pj@teste.com',
            'fornecedor_cgm' => 12,
            'pc60_bloqueado' => 'f',
            'pc60_inscriestadual' => '',
            'pc60_numeroregistro' => '',
            'pc60_orgaoreg' => '',
        ]);

        $service = new ValidacaoCredorService($repository);
        $resultado = $service->validarPorCgm(12);

        $this->assertSame('PENDENTE_DOCUMENTAL', $resultado['status']);
        $this->assertFalse($resultado['apto']);
        $this->assertCount(2, $resultado['pendencias']);
    }
}

