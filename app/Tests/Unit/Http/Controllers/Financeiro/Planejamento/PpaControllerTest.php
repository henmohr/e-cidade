<?php

namespace App\Tests\Unit\Http\Controllers\Financeiro\Planejamento;

use App\Http\Controllers\Financeiro\Planejamento\PpaController;
use App\Http\Requests\Financeiro\Planejamento\ListPpaOrcamentoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaConfrontoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaConsolidacaoEntidadesRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaRelatorioGerencialRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaRelatorioObrigatorioRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaCompatibilizacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaAvaliacaoResultadosRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaIndicadoresAplicacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAlteracaoReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAudienciaPublicaAnexoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAudienciaPublicaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaMetaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaPlanoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaProgramaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaRateioReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaImportacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaImportacaoLoaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaTransferenciaFinanceiraRequest;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaAlteracaoReceitaResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaAudienciaPublicaResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaCadastroResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaConfrontoReceitaDespesaResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaConsolidacaoEntidadesResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaImportacaoResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaImportacaoLoaResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaOrcamentoResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaProjecaoResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaRateioReceitaResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaTransferenciaFinanceiraResultado;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaVersaoResultado;
use App\Services\Financeiro\Planejamento\Ppa\PpaAlteracaoReceitaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaAudienciaPublicaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaCadastroService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConfrontoReceitaDespesaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConsolidacaoEntidadesService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRelatorioGerencialService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRelatoriosObrigatoriosService;
use App\Services\Financeiro\Planejamento\Ppa\PpaCompatibilizacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaAvaliacaoResultadosService;
use App\Services\Financeiro\Planejamento\Ppa\PpaIndicadoresAplicacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConsultaOrcamentoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaImportacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaImportacaoLoaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaProjecaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRateioReceitaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaTransferenciaFinanceiraService;
use App\Services\Financeiro\Planejamento\Ppa\PpaVersaoService;
use Illuminate\Http\Request;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PpaControllerTest extends TestCase
{
    public function testStorePlanoRetorna201ComPayloadPadrao(): void
    {
        $request = new class extends StorePpaPlanoRequest {
            public function validated()
            {
                return [
                    'descricao' => 'PPA 2026-2029',
                    'exercicio_inicial' => 2026,
                    'exercicio_final' => 2029,
                    'grau_plano_contas' => '5',
                    'vinculos_tce' => [['codigo_vinculo' => '100']],
                ];
            }
        };

        $service = $this->createMock(PpaCadastroService::class);
        $service->method('criarPlano')->willReturn(new PpaCadastroResultado(
            1,
            'PPA-2026-2029',
            'em_elaboracao',
            10,
            'Plano PPA criado com sucesso.'
        ));

        $controller = new PpaController();
        $response = $controller->storePlano($request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(1, $dados['data']['plano_id']);
    }

    public function testStorePlanoRetorna422QuandoServicoLancaErro(): void
    {
        $request = new class extends StorePpaPlanoRequest {
            public function validated()
            {
                return [
                    'descricao' => 'PPA',
                    'exercicio_inicial' => 2026,
                    'exercicio_final' => 2027,
                    'grau_plano_contas' => '9',
                    'vinculos_tce' => [['codigo_vinculo' => '100']],
                ];
            }
        };

        $service = $this->createMock(PpaCadastroService::class);
        $service->method('criarPlano')->willThrowException(
            new InvalidArgumentException('Falha de validacao do PPA')
        );

        $controller = new PpaController();
        $response = $controller->storePlano($request, $service);

        $this->assertSame(422, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('erro', $dados['status']);
    }

    public function testStoreVersaoRetorna201(): void
    {
        $service = $this->createMock(PpaVersaoService::class);
        $service->method('criarNovaVersao')->willReturn(new PpaVersaoResultado(
            5,
            1,
            2,
            'em_elaboracao',
            'Nova versao criada com sucesso.',
            null,
            []
        ));

        $controller = new PpaController();
        $response = $controller->storeVersao(1, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame(2, $dados['data']['numero_versao']);
    }

    public function testShowVersaoRetorna404QuandoNaoEncontrada(): void
    {
        $service = $this->createMock(PpaVersaoService::class);
        $service->method('consultarVersao')->willThrowException(
            new InvalidArgumentException('Versao nao encontrada')
        );

        $controller = new PpaController();
        $response = $controller->showVersao(999, $service);

        $this->assertSame(404, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('erro', $dados['status']);
    }

    public function testStoreProgramaStoreReceitaStoreMetaEPublicar(): void
    {
        $programaRequest = new class extends StorePpaProgramaRequest {
            public function validated()
            {
                return ['codigo' => 'P1', 'objetivo' => 'Obj'];
            }
        };

        $receitaRequest = new class extends StorePpaReceitaRequest {
            public function validated()
            {
                return [
                    'conta_receita' => '1.1.1.1',
                    'fonte_recurso' => '1500',
                    'exercicio' => 2026,
                    'valor_previsto' => 100,
                    'valor_atualizado' => 100,
                ];
            }
        };

        $metaRequest = new class extends StorePpaMetaRequest {
            public function validated()
            {
                return [
                    'destinacao_recurso' => '1500',
                    'exercicio' => 2026,
                    'meta_fisica' => 1,
                    'meta_financeira' => 100,
                ];
            }
        };

        $service = $this->createMock(PpaVersaoService::class);
        $service->method('adicionarPrograma')->willReturn(new PpaVersaoResultado(1, 1, 1, 'em_elaboracao', 'ok', null, []));
        $service->method('adicionarReceita')->willReturn(new PpaVersaoResultado(1, 1, 1, 'em_elaboracao', 'ok', null, []));
        $service->method('adicionarMeta')->willReturn(new PpaVersaoResultado(1, 1, 1, 'em_elaboracao', 'ok', null, []));
        $service->method('publicarVersao')->willReturn(new PpaVersaoResultado(1, 1, 1, 'publicada', 'ok', date('c'), []));

        $controller = new PpaController();

        $this->assertSame(201, $controller->storePrograma(1, $programaRequest, $service)->getStatusCode());
        $this->assertSame(201, $controller->storeReceita(1, $receitaRequest, $service)->getStatusCode());
        $this->assertSame(201, $controller->storeMeta(1, $metaRequest, $service)->getStatusCode());
        $this->assertSame(200, $controller->publicarVersao(1, $service)->getStatusCode());
    }

    public function testRatearReceitaRetorna201(): void
    {
        $request = new class extends StorePpaRateioReceitaRequest {
            public function validated()
            {
                return [
                    'conta_receita' => '1.1.1.1',
                    'exercicio' => 2026,
                    'valor_total' => 1000,
                    'rateios' => [
                        ['fonte_recurso' => '1500', 'percentual' => 60],
                        ['fonte_recurso' => '1704', 'percentual' => 40],
                    ],
                ];
            }
        };

        $service = $this->createMock(PpaRateioReceitaService::class);
        $service->method('ratear')->willReturn(new PpaRateioReceitaResultado(
            2,
            '1.1.1.1',
            2026,
            1000,
            [
                ['fonte_recurso' => '1500', 'valor_distribuido' => 600],
                ['fonte_recurso' => '1704', 'valor_distribuido' => 400],
            ],
            'ok'
        ));

        $controller = new PpaController();
        $response = $controller->ratearReceita(2, $request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(1000, $dados['data']['valor_total']);
    }

    public function testImportarVersaoRetorna200(): void
    {
        $request = new class extends StorePpaImportacaoRequest {
            public function validated()
            {
                return [
                    'versao_origem_id' => 1,
                    'opcoes' => ['programas' => true],
                ];
            }
        };

        $service = $this->createMock(PpaImportacaoService::class);
        $service->method('importarVersao')->willReturn(new PpaImportacaoResultado(
            1,
            2,
            ['programas' => 1, 'receitas' => 0, 'metas' => 0, 'vinculos' => 0],
            'ok'
        ));

        $controller = new PpaController();
        $response = $controller->importarVersao(2, $request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(1, $dados['data']['totais']['programas']);
    }

    public function testImportarLoaRetorna200(): void
    {
        $request = new class extends StorePpaImportacaoLoaRequest {
            public function validated()
            {
                return [
                    'exercicio_loa' => 2026,
                    'entidade_id' => 10,
                    'importar_vinculos' => true,
                    'importar_programas' => true,
                    'importar_receitas' => true,
                    'importar_despesas' => true,
                ];
            }
        };

        $service = $this->createMock(PpaImportacaoLoaService::class);
        $service->method('importar')->willReturn(new PpaImportacaoLoaResultado(
            2,
            2026,
            ['vinculos' => 1, 'programas' => 1, 'receitas' => 1, 'despesas' => 1],
            'ok'
        ));

        $controller = new PpaController();
        $response = $controller->importarLoa(2, $request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(1, $dados['data']['totais']['vinculos']);
    }

    public function testProjetarVersaoRetorna200(): void
    {
        $service = $this->createMock(PpaProjecaoService::class);
        $service->method('projetarPorVersao')->willReturn(new PpaProjecaoResultado(
            2,
            [['exercicio' => 2026, 'receita_prevista' => 100, 'despesa_prevista' => 80, 'saldo_previsto' => 20]],
            100,
            80,
            20
        ));

        $controller = new PpaController();
        $response = $controller->projetarVersao(2, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(20, $dados['data']['saldo_total']);
    }

    public function testStoreAlteracaoReceitaRetorna201(): void
    {
        $request = new class extends StorePpaAlteracaoReceitaRequest {
            public function validated()
            {
                return [
                    'conta_receita' => '1.1.1.1',
                    'fonte_recurso' => '1500',
                    'exercicio' => 2026,
                    'tipo_alteracao' => 'suplementacao',
                    'valor_alteracao' => 25.5,
                ];
            }
        };

        $service = $this->createMock(PpaAlteracaoReceitaService::class);
        $service->method('registrarAlteracao')->willReturn(new PpaAlteracaoReceitaResultado(
            2,
            'ok',
            ['alteracao' => ['id' => 1]]
        ));

        $controller = new PpaController();
        $response = $controller->storeAlteracaoReceita(2, $request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['versao_id']);
    }

    public function testListAlteracoesReceitaRetorna200(): void
    {
        $service = $this->createMock(PpaAlteracaoReceitaService::class);
        $service->method('consultarAlteracoes')->willReturn(new PpaAlteracaoReceitaResultado(
            2,
            'ok',
            ['alteracoes' => [['id' => 10], ['id' => 11]]],
            2
        ));

        $request = new Request(['conta_receita' => '1.1.1.1']);
        $controller = new PpaController();
        $response = $controller->listAlteracoesReceita(2, $request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['total_registros']);
    }

    public function testConsultarOrcamentoRetorna200(): void
    {
        $request = new class extends ListPpaOrcamentoRequest {
            public function validated()
            {
                return ['ate_data' => '2026-02-15'];
            }
        };

        $service = $this->createMock(PpaConsultaOrcamentoService::class);
        $service->method('consultarPorVersao')->willReturn(new PpaOrcamentoResultado(
            2,
            '2026-02-15',
            130,
            80,
            50,
            [['conta_receita' => '1.1.1.1', 'valor_atualizado' => 130]],
            [['codigo_reduzido' => 'X', 'meta_financeira' => 80]]
        ));

        $controller = new PpaController();
        $response = $controller->consultarOrcamento(2, $request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(50, $dados['data']['saldo']);
    }

    public function testStoreTransferenciaFinanceiraRetorna201(): void
    {
        $request = new class extends StorePpaTransferenciaFinanceiraRequest {
            public function validated()
            {
                return [
                    'entidade_destino_id' => 7,
                    'exercicio' => 2026,
                    'valor_previsto' => 1000,
                ];
            }
        };

        $service = $this->createMock(PpaTransferenciaFinanceiraService::class);
        $service->method('cadastrar')->willReturn(new PpaTransferenciaFinanceiraResultado(
            2,
            'ok',
            ['transferencia' => ['id' => 1]]
        ));

        $controller = new PpaController();
        $response = $controller->storeTransferenciaFinanceira(2, $request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['versao_id']);
    }

    public function testListTransferenciasFinanceirasRetorna200(): void
    {
        $service = $this->createMock(PpaTransferenciaFinanceiraService::class);
        $service->method('listar')->willReturn(new PpaTransferenciaFinanceiraResultado(
            2,
            'ok',
            ['transferencias' => [['id' => 1], ['id' => 2]]],
            2
        ));

        $controller = new PpaController();
        $response = $controller->listTransferenciasFinanceiras(2, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['total_registros']);
    }

    public function testConfrontarReceitaDespesaRetorna200(): void
    {
        $request = new class extends ListPpaConfrontoRequest {
            public function validated()
            {
                return ['versoes_ids' => [1, 2], 'ate_data' => '2026-02-15'];
            }
        };

        $service = $this->createMock(PpaConfrontoReceitaDespesaService::class);
        $service->method('confrontar')->willReturn(new PpaConfrontoReceitaDespesaResultado(
            [1, 2],
            '2026-02-15',
            180,
            120,
            60,
            [['fonte_recurso' => '1500', 'receita' => 100, 'despesa' => 70, 'saldo' => 30]]
        ));

        $controller = new PpaController();
        $response = $controller->confrontarReceitaDespesa($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(60, $dados['data']['saldo_total']);
    }

    public function testConsolidarEntidadesRetorna200(): void
    {
        $request = new class extends ListPpaConsolidacaoEntidadesRequest {
            public function validated()
            {
                return ['versoes_ids' => [1, 2], 'entidades_ids' => [10, 20], 'ate_data' => '2026-02-15'];
            }
        };

        $service = $this->createMock(PpaConsolidacaoEntidadesService::class);
        $service->method('consolidar')->willReturn(new PpaConsolidacaoEntidadesResultado(
            [1, 2],
            [10, 20],
            '2026-02-15',
            250,
            130,
            5,
            55,
            [
                ['entidade_id' => 10, 'receita' => 140, 'despesa' => 80],
                ['entidade_id' => 20, 'receita' => 110, 'despesa' => 50],
            ]
        ));

        $controller = new PpaController();
        $response = $controller->consolidarEntidades($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(250, $dados['data']['total_receita']);
    }

    public function testRelatorioGerencialRetorna200(): void
    {
        $request = new class extends ListPpaRelatorioGerencialRequest {
            public function validated()
            {
                return ['versoes_ids' => [1], 'entidades_ids' => [10, 20], 'ate_data' => '2026-02-15'];
            }
        };

        $service = $this->createMock(PpaRelatorioGerencialService::class);
        $service->method('gerar')->willReturn(new \App\Services\Financeiro\Planejamento\Ppa\Dto\PpaRelatorioGerencialResultado(
            [1],
            [10, 20],
            '2026-02-15',
            150,
            70,
            30,
            [['fonte_recurso' => '1500', 'valor' => 100]],
            [['destinacao_recurso' => '1500', 'valor' => 70]],
            [['entidade_id' => 10, 'entidade_destino_id' => 20, 'valor' => 30]]
        ));

        $controller = new PpaController();
        $response = $controller->relatorioGerencial($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(150, $dados['data']['totais']['receitas']);
    }

    public function testRelatorioObrigatorioRetorna200(): void
    {
        $request = new class extends ListPpaRelatorioObrigatorioRequest {
            public function validated()
            {
                return ['tipo' => 'a', 'versoes_ids' => [1], 'entidades_ids' => [10], 'ate_data' => '2026-02-15'];
            }
        };

        $service = $this->createMock(PpaRelatoriosObrigatoriosService::class);
        $service->method('gerar')->willReturn(new \App\Services\Financeiro\Planejamento\Ppa\Dto\PpaRelatorioObrigatorioResultado(
            'a',
            'Demonstrativo das Receitas',
            [1],
            [10],
            '2026-02-15',
            [['fonte_recurso' => '1500', 'valor' => 100]]
        ));

        $controller = new PpaController();
        $response = $controller->relatorioObrigatorio($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame('a', $dados['data']['tipo']);
    }

    public function testCompatibilizacaoRetorna200(): void
    {
        $request = new class extends ListPpaCompatibilizacaoRequest {
            public function validated()
            {
                return ['versao_ppa_id' => 2, 'exercicio_ldo' => 2026, 'exercicio_loa' => 2026, 'instituicao_id' => 1];
            }
        };

        $service = $this->createMock(PpaCompatibilizacaoService::class);
        $service->method('gerar')->willReturn(new \App\Services\Financeiro\Planejamento\Ppa\Dto\PpaCompatibilizacaoResultado(
            2,
            2026,
            2026,
            [['fonte_recurso' => '1500', 'diferenca_ppa_ldo' => 10, 'diferenca_ppa_loa' => 20]],
            [['programa' => '0001', 'acao' => '2001', 'diferenca_ppa_ldo' => 10, 'diferenca_ppa_loa' => 20]]
        ));

        $controller = new PpaController();
        $response = $controller->compatibilizacao($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['versao_ppa_id']);
    }

    public function testAvaliacaoResultadosRetorna200(): void
    {
        $request = new class extends ListPpaAvaliacaoResultadosRequest {
            public function validated()
            {
                return ['versao_id' => 2, 'exercicios' => [2026], 'entidades_ids' => [10]];
            }
        };

        $service = $this->createMock(PpaAvaliacaoResultadosService::class);
        $service->method('gerar')->willReturn(new \App\Services\Financeiro\Planejamento\Ppa\Dto\PpaAvaliacaoResultadosResultado(
            2,
            [2026],
            [[
                'programa' => '0001',
                'acao' => '2001',
                'programacao_financeira' => 100,
                'execucao_financeira' => 80,
            ]]
        ));

        $controller = new PpaController();
        $response = $controller->avaliacaoResultados($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['versao_id']);
    }

    public function testIndicadoresAplicacaoRetorna200(): void
    {
        $request = new class extends ListPpaIndicadoresAplicacaoRequest {
            public function validated()
            {
                return ['versao_id' => 2, 'exercicios' => [2026], 'entidades_ids' => [10]];
            }
        };

        $service = $this->createMock(PpaIndicadoresAplicacaoService::class);
        $service->method('gerar')->willReturn(new \App\Services\Financeiro\Planejamento\Ppa\Dto\PpaIndicadoresAplicacaoResultado(
            2,
            [2026],
            300,
            [
                'saude' => ['valor' => 60, 'percentual' => 20],
                'educacao' => ['valor' => 75, 'percentual' => 25],
                'pessoal' => ['valor' => 90, 'percentual' => 30],
            ]
        ));

        $controller = new PpaController();
        $response = $controller->indicadoresAplicacao($request, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['versao_id']);
        $this->assertSame(20, $dados['data']['indicadores']['saude']['percentual']);
    }

    public function testStoreAudienciaPublicaRetorna201(): void
    {
        $request = new class extends StorePpaAudienciaPublicaRequest {
            public function validated()
            {
                return [
                    'data_realizacao' => '2026-02-19',
                    'solicitacoes_comunidade' => 'Pavimentacao e iluminacao.',
                    'bairro_atendido' => 'Centro',
                    'contato_solicitante' => '11999999999',
                    'orgao_responsavel' => 'Secretaria de Planejamento',
                    'status' => 'em_analise',
                ];
            }
        };

        $service = $this->createMock(PpaAudienciaPublicaService::class);
        $service->method('registrarAudiencia')->willReturn(new PpaAudienciaPublicaResultado(
            2,
            'ok',
            ['audiencia' => ['id' => 10, 'status' => 'em_analise']]
        ));

        $controller = new PpaController();
        $response = $controller->storeAudienciaPublica(2, $request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(10, $dados['data']['dados']['audiencia']['id']);
    }

    public function testStoreAudienciaPublicaAnexoRetorna201(): void
    {
        $request = new class extends StorePpaAudienciaPublicaAnexoRequest {
            public function validated()
            {
                return [
                    'nome_arquivo' => 'ata-audiencia.pdf',
                    'conteudo_base64' => base64_encode('conteudo'),
                    'mime_type' => 'application/pdf',
                ];
            }
        };

        $service = $this->createMock(PpaAudienciaPublicaService::class);
        $service->method('anexarAta')->willReturn(new PpaAudienciaPublicaResultado(
            2,
            'ok',
            ['anexo' => ['id' => 100]]
        ));

        $controller = new PpaController();
        $response = $controller->storeAudienciaPublicaAnexo(10, $request, $service);

        $this->assertSame(201, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(100, $dados['data']['dados']['anexo']['id']);
    }

    public function testListAudienciasPublicasRetorna200(): void
    {
        $service = $this->createMock(PpaAudienciaPublicaService::class);
        $service->method('listarAudiencias')->willReturn(new PpaAudienciaPublicaResultado(
            2,
            'ok',
            ['audiencias' => [['id' => 10], ['id' => 11]]],
            2
        ));

        $controller = new PpaController();
        $response = $controller->listAudienciasPublicas(2, $service);

        $this->assertSame(200, $response->getStatusCode());
        $dados = $response->getData(true);
        $this->assertSame('ok', $dados['status']);
        $this->assertSame(2, $dados['data']['total_registros']);
    }
}
