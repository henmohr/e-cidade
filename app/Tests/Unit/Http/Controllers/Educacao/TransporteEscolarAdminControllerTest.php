<?php

namespace App\Tests\Unit\Http\Controllers\Educacao;

use App\Http\Controllers\Educacao\TransporteEscolarAdminController;
use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaVeiculoTransporteEscolar;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarAdminService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarCarteiraService;
use App\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransporteEscolarAdminControllerTest extends TestCase
{
    public function testIndexRetornaViewDeGestao(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->method('listagem')->willReturn([
            'linhas' => [
                ['id' => 1, 'codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo_servico' => 'proprio', 'horario_saida' => '06:00', 'horario_retorno' => '12:00', 'custo_mensal' => 1200, 'unidade_escolar' => null, 'rota_descricao' => null, 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto A -> Ponto B', 'roteiro_detalhado' => ['1. Ponto A', '2. Ponto B'], 'ativo' => true],
            ],
            'pontos' => [
                ['id' => 3, 'linha_id' => 1, 'linha_codigo' => 'TRE-01', 'nome' => 'Ponto Central', 'endereco' => 'Rua A', 'tipo_ponto' => 'parada', 'ordem' => 1, 'observacao' => null, 'ativo' => true],
            ],
            'veiculos' => [
                ['id' => 2, 'placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista_nome' => 'Joao', 'capacidade' => 20, 'situacao' => 'disponivel', 'observacao' => null],
            ],
            'vinculos' => [],
            'alunos' => [],
        ]);
        $service->expects($this->never())->method('obterLinha');
        $service->expects($this->never())->method('obterVeiculo');
        $service->expects($this->never())->method('obterAluno');
        $service->expects($this->never())->method('obterVinculo');
        $service->expects($this->never())->method('obterPonto');

        $controller = new TransporteEscolarAdminController();
        $response = $controller->index(Request::create('/web/transporte-escolar/gestao', 'GET'), $service);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('educacao.transporte-escolar.gestao', $response->name());
    }

    public function testStoreLinhaRedirecionaParaGestaoDaLinha(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->expects($this->once())
            ->method('salvarLinha')
            ->with($this->callback(static function (array $dados): bool {
                return $dados['codigo'] === 'TRE-10'
                    && $dados['nome'] === 'Linha Teste'
                    && $dados['tipo_servico'] === 'proprio'
                    && $dados['ativo'] === true
                    && (float) $dados['custo_mensal'] === 100.50;
            }))
            ->willReturn(self::linhaModel(7));

        $request = new class extends Request {
            public function validate(array $rules, ...$params): array
            {
                return [
                    'codigo' => 'TRE-10',
                    'nome' => 'Linha Teste',
                    'tipo_servico' => 'proprio',
                    'horario_saida' => '06:00',
                    'horario_retorno' => '12:00',
                    'custo_mensal' => 100.50,
                    'unidade_escolar' => 'EMEF Centro',
                    'rota_descricao' => 'Percurso principal',
                    'ativo' => true,
                ];
            }
        };

        $controller = new TransporteEscolarAdminController();
        $response = $controller->storeLinha($request, $service);

        $this->assertSame(route('transportescolar.web.gestao', ['linha_id' => 7]), $response->getTargetUrl());
        $this->assertSame('Linha salva com sucesso.', $response->getSession()->get('status'));
    }

    public function testStoreVinculoRedirecionaParaGestaoDoVinculo(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->expects($this->once())
            ->method('salvarVinculo')
            ->with($this->callback(static function (array $dados): bool {
                return $dados['linha_id'] === 1
                    && $dados['veiculo_id'] === 2
                    && $dados['observacao'] === 'Vinculo semanal';
            }))
            ->willReturn(self::vinculoModel(9));

        $request = new class extends Request {
            public function validate(array $rules, ...$params): array
            {
                return [
                    'linha_id' => 1,
                    'veiculo_id' => 2,
                    'data_inicio' => '2026-05-29',
                    'data_fim' => '2026-12-31',
                    'observacao' => 'Vinculo semanal',
                ];
            }
        };

        $controller = new TransporteEscolarAdminController();
        $response = $controller->storeVinculo($request, $service);

        $this->assertSame(route('transportescolar.web.gestao', ['vinculo_id' => 9]), $response->getTargetUrl());
        $this->assertSame('Vinculo salvo com sucesso.', $response->getSession()->get('status'));
    }

    public function testStorePontoRedirecionaParaGestaoDoPonto(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->expects($this->once())
            ->method('salvarPonto')
            ->with($this->callback(static function (array $dados): bool {
                return $dados['linha_id'] === 1
                    && $dados['nome'] === 'Ponto Central'
                    && $dados['tipo_ponto'] === 'parada'
                    && $dados['ordem'] === 1
                    && $dados['ativo'] === true;
            }))
            ->willReturn(self::pontoModel(12));

        $request = new class extends Request {
            public function validate(array $rules, ...$params): array
            {
                return [
                    'linha_id' => 1,
                    'nome' => 'Ponto Central',
                    'endereco' => 'Rua A',
                    'tipo_ponto' => 'parada',
                    'ordem' => 1,
                    'observacao' => 'Proximo a escola',
                    'ativo' => true,
                ];
            }
        };

        $controller = new TransporteEscolarAdminController();
        $response = $controller->storePonto($request, $service);

        $this->assertSame(route('transportescolar.web.gestao', ['ponto_id' => 12]), $response->getTargetUrl());
        $this->assertSame('Ponto salvo com sucesso.', $response->getSession()->get('status'));
    }

    public function testDestroyAlunoRedirecionaComMensagem(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->expects($this->once())->method('removerAluno')->with(11);

        $controller = new TransporteEscolarAdminController();
        $response = $controller->destroyAluno(11, $service);

        $this->assertSame(route('transportescolar.web.gestao'), $response->getTargetUrl());
        $this->assertSame('Aluno removido.', $response->getSession()->get('status'));
    }

    public function testStoreAlunoPersisteFotoQuandoEnviada(): void
    {
        $service = $this->createMock(TransporteEscolarAdminService::class);
        $service->expects($this->once())
            ->method('salvarAluno')
            ->with($this->callback(static function (array $dados): bool {
                return $dados['aluno_nome'] === 'Ana Souza'
                    && $dados['foto_path'] === 'transporte-escolar/alunos/foto-11.jpg'
                    && $dados['utiliza_transporte'] === true;
            }))
            ->willReturn(self::alunoModel(11));

        $request = new class extends Request {
            public function validate(array $rules, ...$params): array
            {
                return [
                    'linha_id' => 1,
                    'aluno_nome' => 'Ana Souza',
                    'aluno_cpf' => '12345678901',
                    'escola_nome' => 'EMEF Centro',
                    'local_embarque' => 'Rua A',
                    'motivo_uso' => 'Acesso escolar',
                    'periodo_uso' => 'Matutino',
                    'situacao_matricula' => 'Ativa',
                    'utiliza_transporte' => true,
                    'foto_aluno' => true,
                ];
            }

            public function file($key = null, $default = null)
            {
                return new class {
                    public function store(string $path, string $disk): string
                    {
                        return 'transporte-escolar/alunos/foto-11.jpg';
                    }
                };
            }
        };

        $controller = new TransporteEscolarAdminController();
        $response = $controller->storeAluno($request, $service);

        $this->assertSame(route('transportescolar.web.gestao', ['aluno_id' => 11]), $response->getTargetUrl());
        $this->assertSame('Aluno salvo com sucesso.', $response->getSession()->get('status'));
    }

    public function testCarteiraAlunoRetornaViewImprimivel(): void
    {
        $service = $this->createMock(TransporteEscolarCarteiraService::class);
        $service->expects($this->once())
            ->method('payload')
            ->with(11)
            ->willReturn([
                'aluno' => (object) [
                    'id' => 11,
                    'aluno_nome' => 'Ana Souza',
                    'aluno_cpf' => '12345678901',
                    'escola_nome' => 'EMEF Centro',
                    'local_embarque' => 'Rua A',
                    'periodo_uso' => 'Matutino',
                    'situacao_matricula' => 'Ativa',
                    'utiliza_transporte' => true,
                ],
                'linha' => (object) [
                    'codigo' => 'TRE-01',
                    'nome' => 'Linha Centro',
                ],
                'codigo_carteira' => 'TES-000011',
                'qr_code' => 'data:image/png;base64,abc',
                'validade_texto' => '29/05/2026',
                'emitido_em' => '29/05/2026 10:00',
            ]);

        $controller = new TransporteEscolarAdminController();
        $response = $controller->carteiraAluno(11, $service);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('educacao.transporte-escolar.carteira', $response->name());
    }

    public function testCarteiraAlunoPdfRetornaArquivoPdf(): void
    {
        $service = $this->createMock(TransporteEscolarCarteiraService::class);
        $service->expects($this->once())
            ->method('payload')
            ->with(11)
            ->willReturn([
                'aluno' => (object) [
                    'id' => 11,
                    'aluno_nome' => 'Ana Souza',
                    'aluno_cpf' => '12345678901',
                    'escola_nome' => 'EMEF Centro',
                    'local_embarque' => 'Rua A',
                    'periodo_uso' => 'Matutino',
                    'situacao_matricula' => 'Ativa',
                    'utiliza_transporte' => true,
                ],
                'linha' => (object) [
                    'codigo' => 'TRE-01',
                    'nome' => 'Linha Centro',
                ],
                'codigo_carteira' => 'TES-000011',
                'qr_code' => 'data:image/png;base64,abc',
                'validade_texto' => '29/05/2026',
                'emitido_em' => '29/05/2026 10:00',
            ]);

        $controller = new TransporteEscolarAdminController();
        $response = $controller->carteiraAlunoPdf(11, $service);

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('carteira-transporte-escolar-11.pdf', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    private static function linhaModel(int $id): LinhaTransporteEscolar
    {
        $linha = new LinhaTransporteEscolar();
        $linha->id = $id;

        return $linha;
    }

    private static function vinculoModel(int $id): LinhaVeiculoTransporteEscolar
    {
        $vinculo = new LinhaVeiculoTransporteEscolar();
        $vinculo->id = $id;

        return $vinculo;
    }

    private static function alunoModel(int $id): AlunoTransporteEscolar
    {
        $aluno = new AlunoTransporteEscolar();
        $aluno->id = $id;

        return $aluno;
    }

    private static function pontoModel(int $id): \App\Models\Educacao\TransporteEscolar\PontoTransporteEscolar
    {
        $ponto = new \App\Models\Educacao\TransporteEscolar\PontoTransporteEscolar();
        $ponto->id = $id;

        return $ponto;
    }
}
