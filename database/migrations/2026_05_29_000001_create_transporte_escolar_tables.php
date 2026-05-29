<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->boolean('utiliza_transporte')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educacao_transporte_escolar_alunos');
        Schema::dropIfExists('educacao_transporte_escolar_linha_veiculos');
        Schema::dropIfExists('educacao_transporte_escolar_veiculos');
        Schema::dropIfExists('educacao_transporte_escolar_linhas');
    }
};
