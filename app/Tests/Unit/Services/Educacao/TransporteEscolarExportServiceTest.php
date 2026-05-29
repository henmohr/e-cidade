<?php

namespace App\Tests\Unit\Services\Educacao;

use App\Services\Educacao\TransporteEscolar\TransporteEscolarExportService;
use App\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransporteEscolarExportServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        DB::reconnect('sqlite');

        $this->criarSchema();
    }

    public function testPayloadEnriqueceRoteiroDaLinha(): void
    {
        DB::table('educacao_transporte_escolar_linhas')->insert([
            'codigo' => 'TRE-01',
            'nome' => 'Linha Centro',
            'tipo_servico' => 'proprio',
            'horario_saida' => '06:00',
            'horario_retorno' => '12:00',
            'custo_mensal' => 1200.00,
            'unidade_escolar' => 'EMEF Centro',
            'rota_descricao' => 'Resumo manual',
            'ativo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('educacao_transporte_escolar_pontos')->insert([
            [
                'linha_id' => 1,
                'nome' => 'Ponto Central',
                'endereco' => 'Rua A',
                'tipo_ponto' => 'parada',
                'ordem' => 1,
                'observacao' => null,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'linha_id' => 1,
                'nome' => 'Terminal',
                'endereco' => 'Rua B',
                'tipo_ponto' => 'terminal',
                'ordem' => 2,
                'observacao' => null,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $service = new TransporteEscolarExportService();
        $payload = $service->payload([]);
        $csv = $service->csv([]);

        $this->assertCount(1, $payload['linhas']);
        $this->assertSame('TRE-01', $payload['linhas'][0]['codigo']);
        $this->assertSame(2, $payload['linhas'][0]['pontos_total']);
        $this->assertSame('Ponto Central -> Terminal', $payload['linhas'][0]['roteiro_resumido']);
        $this->assertStringContainsString('roteiro=Ponto Central -> Terminal', $csv);
    }

    private function criarSchema(): void
    {
        Schema::dropIfExists('educacao_transporte_escolar_pontos');
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
    }
}
