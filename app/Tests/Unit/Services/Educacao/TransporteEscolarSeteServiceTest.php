<?php

namespace App\Tests\Unit\Services\Educacao;

use App\Services\Educacao\TransporteEscolar\TransporteEscolarExportService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarSeteService;
use App\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransporteEscolarSeteServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        DB::reconnect('sqlite');

        $pdo = DB::connection()->getPdo();
        if (method_exists($pdo, 'sqliteCreateFunction')) {
            $sequencias = [];
            $pdo->sqliteCreateFunction('nextval', function ($sequence) use (&$sequencias) {
                if (!isset($sequencias[$sequence])) {
                    $sequencias[$sequence] = 1;
                }

                return $sequencias[$sequence]++;
            }, 1);
        }

        $this->criarSchema();
    }

    public function testExportarJsonEstruturaSete(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->expects($this->once())
            ->method('payload')
            ->with([])
            ->willReturn([
                'linhas' => [
                    [
                        'codigo' => 'TRE-01',
                        'nome' => 'Linha Centro',
                        'tipo' => 'proprio',
                        'horario' => '06:40 / 11:30',
                        'custo' => 'R$ 3.420,00',
                        'unidade_escolar' => 'EMEI Esperanca',
                        'pontos_total' => 2,
                        'roteiro_resumido' => 'Ponto Central -> Terminal',
                    ],
                ],
                'veiculos' => [
                    [
                        'placa' => 'RIO1A23',
                        'modelo' => 'Microonibus 28 lugares',
                        'motorista' => 'Carlos Henrique',
                        'status' => 'disponivel',
                    ],
                ],
                'pontos' => [
                    [
                        'linha_codigo' => 'TRE-01',
                        'nome' => 'Ponto Central',
                        'endereco' => 'Rua Central, 101',
                        'tipo_ponto' => 'parada',
                        'ordem' => 1,
                    ],
                ],
                'alunos' => [
                    [
                        'cpf' => '12345678901',
                        'nome' => 'Ana Souza',
                        'escola' => 'EMEI Esperanca',
                        'linha' => 'TRE-01',
                        'embarque' => 'Rua Central, 101',
                        'periodo_uso' => 'Manha',
                    ],
                ],
            ]);

        $service = new TransporteEscolarSeteService($export);
        $dados = json_decode($service->exportarJson([]), true);

        $this->assertIsArray($dados);
        $this->assertArrayHasKey('metadados', $dados);
        $this->assertArrayHasKey('linhas', $dados);
        $this->assertArrayHasKey('veiculos', $dados);
        $this->assertArrayHasKey('pontos', $dados);
        $this->assertArrayHasKey('vinculos', $dados);
        $this->assertArrayHasKey('alunos', $dados);
        $this->assertSame('TRE-01', $dados['linhas'][0]['codigo']);
        $this->assertSame(2, $dados['linhas'][0]['pontos_total']);
        $this->assertSame('Ponto Central -> Terminal', $dados['linhas'][0]['roteiro_resumido']);
        $this->assertSame('RIO1A23', $dados['veiculos'][0]['placa']);
        $this->assertSame('Ponto Central', $dados['pontos'][0]['nome']);
        $this->assertSame('12345678901', $dados['alunos'][0]['aluno_cpf']);
    }

    public function testImportarJsonPersisteRegistros(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->method('payload')->willReturn([
            'linhas' => [],
            'veiculos' => [],
            'pontos' => [],
            'alunos' => [],
        ]);

        $service = new TransporteEscolarSeteService($export);

        $resumo = $service->importarJson(json_encode([
            'linhas' => [
                [
                    'codigo' => 'TRE-01',
                    'nome' => 'Linha Centro',
                    'tipo_servico' => 'proprio',
                    'horario_saida' => '06:40',
                    'horario_retorno' => '11:30',
                    'custo_mensal' => 3420.00,
                    'unidade_escolar' => 'EMEI Esperanca',
                    'rota_descricao' => 'Rota central',
                    'ativo' => true,
                ],
            ],
            'veiculos' => [
                [
                    'placa' => 'RIO1A23',
                    'modelo' => 'Microonibus 28 lugares',
                    'motorista_nome' => 'Carlos Henrique',
                    'capacidade' => 28,
                    'situacao' => 'disponivel',
                    'observacao' => 'Reserva tecnica',
                ],
            ],
            'pontos' => [
                [
                    'linha_codigo' => 'TRE-01',
                    'nome' => 'Ponto Central',
                    'endereco' => 'Rua Central, 101',
                    'tipo_ponto' => 'parada',
                    'ordem' => 1,
                    'observacao' => 'Ponto principal',
                    'ativo' => true,
                ],
            ],
            'vinculos' => [
                [
                    'linha_codigo' => 'TRE-01',
                    'veiculo_placa' => 'RIO1A23',
                    'data_inicio' => '2026-01-10',
                    'data_fim' => '2026-12-31',
                    'observacao' => 'Operacao regular',
                ],
            ],
            'alunos' => [
                [
                    'aluno_cpf' => '12345678901',
                    'aluno_nome' => 'Ana Souza',
                    'escola_nome' => 'EMEI Esperanca',
                    'local_embarque' => 'Rua Central, 101',
                    'motivo_uso' => 'Proximidade',
                    'periodo_uso' => 'Manha',
                    'situacao_matricula' => 'Ativo',
                    'utiliza_transporte' => true,
                    'linha_codigo' => 'TRE-01',
                ],
            ],
        ], JSON_UNESCAPED_UNICODE));

        $this->assertSame(1, $resumo['linhas_criadas']);
        $this->assertSame(1, $resumo['veiculos_criados']);
        $this->assertSame(1, $resumo['pontos_criados']);
        $this->assertSame(1, $resumo['vinculos_criados']);
        $this->assertSame(1, $resumo['alunos_criados']);

        $this->assertDatabaseHas('educacao_transporte_escolar_linhas', [
            'codigo' => 'TRE-01',
            'nome' => 'Linha Centro',
        ]);
        $this->assertDatabaseHas('educacao_transporte_escolar_veiculos', [
            'placa' => 'RIO1A23',
        ]);
        $this->assertDatabaseHas('educacao_transporte_escolar_pontos', [
            'nome' => 'Ponto Central',
            'ordem' => 1,
        ]);
        $this->assertDatabaseHas('educacao_transporte_escolar_linha_veiculos', [
            'observacao' => 'Operacao regular',
        ]);
        $this->assertDatabaseHas('educacao_transporte_escolar_alunos', [
            'aluno_cpf' => '12345678901',
            'aluno_nome' => 'Ana Souza',
        ]);
    }

    private function criarSchema(): void
    {
        Schema::dropIfExists('educacao_transporte_escolar_alunos');
        Schema::dropIfExists('educacao_transporte_escolar_pontos');
        Schema::dropIfExists('educacao_transporte_escolar_linha_veiculos');
        Schema::dropIfExists('educacao_transporte_escolar_veiculos');
        Schema::dropIfExists('educacao_transporte_escolar_linhas');

        Schema::create('educacao_transporte_escolar_linhas', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nome', 180);
            $table->string('tipo_servico', 40);
            $table->string('horario_saida', 20)->nullable();
            $table->string('horario_retorno', 20)->nullable();
            $table->decimal('custo_mensal', 12, 2)->default(0);
            $table->string('unidade_escolar', 180)->nullable();
            $table->text('rota_descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('educacao_transporte_escolar_veiculos', function (Blueprint $table): void {
            $table->id();
            $table->string('placa', 20)->unique();
            $table->string('modelo', 180);
            $table->string('motorista_nome', 180)->nullable();
            $table->unsignedInteger('capacidade')->nullable();
            $table->string('situacao', 40)->default('disponivel');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        Schema::create('educacao_transporte_escolar_pontos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('linha_id')->constrained('educacao_transporte_escolar_linhas')->cascadeOnDelete();
            $table->string('nome', 180);
            $table->string('endereco', 255)->nullable();
            $table->string('tipo_ponto', 40)->default('parada');
            $table->unsignedInteger('ordem')->default(0);
            $table->text('observacao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('educacao_transporte_escolar_linha_veiculos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('linha_id')->constrained('educacao_transporte_escolar_linhas')->cascadeOnDelete();
            $table->foreignId('veiculo_id')->constrained('educacao_transporte_escolar_veiculos')->cascadeOnDelete();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });

        Schema::create('educacao_transporte_escolar_alunos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('linha_id')->nullable()->constrained('educacao_transporte_escolar_linhas')->nullOnDelete();
            $table->unsignedBigInteger('cgm_id')->nullable();
            $table->string('aluno_nome', 180);
            $table->string('aluno_cpf', 20)->nullable();
            $table->string('escola_nome', 180)->nullable();
            $table->string('local_embarque', 180)->nullable();
            $table->string('motivo_uso', 180)->nullable();
            $table->string('periodo_uso', 80)->nullable();
            $table->string('situacao_matricula', 40)->nullable();
            $table->string('foto_path', 255)->nullable();
            $table->boolean('utiliza_transporte')->default(true);
            $table->timestamps();
        });
    }
}
