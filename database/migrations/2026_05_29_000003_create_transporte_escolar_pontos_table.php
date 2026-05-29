<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

    public function down(): void
    {
        Schema::dropIfExists('educacao_transporte_escolar_pontos');
    }
};
