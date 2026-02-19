<?php

namespace App\Tests\Unit\Services\Financeiro\Planejamento\Ppa;

use App\Repositories\Financeiro\Planejamento\Ppa\PpaAudienciaRepositoryInterface;
use App\Repositories\Financeiro\Planejamento\Ppa\PpaRepositoryInterface;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaValidacaoResultado;
use App\Services\Financeiro\Planejamento\Ppa\PpaAudienciaPublicaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaValidacaoService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PpaAudienciaPublicaServiceTest extends TestCase
{
    public function testRegistrarAudienciaComSucesso(): void
    {
        $ppaRepo = $this->createMock(PpaRepositoryInterface::class);
        $audRepo = $this->createMock(PpaAudienciaRepositoryInterface::class);
        $validacao = $this->createMock(PpaValidacaoService::class);

        $ppaRepo->method('obterVersaoPorId')->with(2)->willReturn([
            'id' => 2,
            'status' => 'em_elaboracao',
        ]);

        $validacao->method('validarCadastro')->willReturn(new PpaValidacaoResultado(true, []));

        $audRepo->method('criarAudiencia')->willReturn([
            'id' => 10,
            'ppa_versao_id' => 2,
            'data_realizacao' => '2026-02-19',
            'solicitacoes_comunidade' => 'Solicitacao',
            'bairro_atendido' => 'Centro',
            'contato_solicitante' => '11999999999',
            'orgao_responsavel' => 'Planejamento',
            'status' => 'em_analise',
        ]);

        $service = new PpaAudienciaPublicaService($ppaRepo, $audRepo, $validacao);
        $resultado = $service->registrarAudiencia(2, [
            'data_realizacao' => '2026-02-19',
            'solicitacoes_comunidade' => 'Solicitacao',
            'bairro_atendido' => 'Centro',
            'contato_solicitante' => '11999999999',
            'orgao_responsavel' => 'Planejamento',
            'status' => 'em_analise',
        ]);

        $this->assertSame(2, $resultado->versaoId);
        $this->assertSame(10, $resultado->dados['audiencia']['id']);
    }

    public function testListarAudienciasRetornaTotal(): void
    {
        $ppaRepo = $this->createMock(PpaRepositoryInterface::class);
        $audRepo = $this->createMock(PpaAudienciaRepositoryInterface::class);

        $ppaRepo->method('obterVersaoPorId')->with(2)->willReturn(['id' => 2]);
        $audRepo->method('listarAudienciasPorVersao')->with(2)->willReturn([
            ['id' => 10, 'ppa_versao_id' => 2],
            ['id' => 11, 'ppa_versao_id' => 2],
        ]);

        $service = new PpaAudienciaPublicaService($ppaRepo, $audRepo);
        $resultado = $service->listarAudiencias(2);

        $this->assertSame(2, $resultado->totalRegistros);
    }

    public function testBloqueiaRegistroQuandoVersaoPublicada(): void
    {
        $ppaRepo = $this->createMock(PpaRepositoryInterface::class);
        $audRepo = $this->createMock(PpaAudienciaRepositoryInterface::class);
        $validacao = $this->createMock(PpaValidacaoService::class);

        $ppaRepo->method('obterVersaoPorId')->willReturn(['id' => 2, 'status' => 'publicada']);
        $validacao->method('validarCadastro')->willReturn(new PpaValidacaoResultado(false, [['mensagem' => 'bloqueio']]));

        $service = new PpaAudienciaPublicaService($ppaRepo, $audRepo, $validacao);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nao e permitido alterar uma versao publicada.');

        $service->registrarAudiencia(2, [
            'data_realizacao' => '2026-02-19',
            'solicitacoes_comunidade' => 'Solicitacao',
            'bairro_atendido' => 'Centro',
            'contato_solicitante' => '11999999999',
            'orgao_responsavel' => 'Planejamento',
            'status' => 'em_analise',
        ]);
    }

    public function testListarAnexosFalhaSemAudiencia(): void
    {
        $ppaRepo = $this->createMock(PpaRepositoryInterface::class);
        $audRepo = $this->createMock(PpaAudienciaRepositoryInterface::class);

        $audRepo->method('obterAudienciaPorId')->with(999)->willReturn(null);

        $service = new PpaAudienciaPublicaService($ppaRepo, $audRepo);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Audiencia publica nao encontrada para consulta de anexos.');

        $service->listarAnexos(999);
    }
}
