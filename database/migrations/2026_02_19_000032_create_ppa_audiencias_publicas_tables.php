<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePpaAudienciasPublicasTables extends Migration
{
    public function up(): void
    {
        Schema::create('ppa_audiencias_publicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ppa_versao_id');
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->date('data_realizacao');
            $table->text('solicitacoes_comunidade');
            $table->string('bairro_atendido', 120);
            $table->string('contato_solicitante', 255);
            $table->string('orgao_responsavel', 255);
            $table->string('status', 30)->default('recebida');
            $table->text('observacao')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('ppa_versao_id')
                ->references('id')
                ->on('ppa_versoes')
                ->onDelete('cascade');

            $table->index(['ppa_versao_id', 'status'], 'ppa_aud_pub_idx_versao_status');
            $table->index(['data_realizacao'], 'ppa_aud_pub_idx_data_realizacao');
        });

        Schema::create('ppa_audiencias_publicas_anexos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ppa_audiencia_publica_id');
            $table->string('nome_original', 255);
            $table->string('nome_arquivo', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamanho_bytes');
            $table->string('storage_disk', 40)->default('local');
            $table->string('storage_path', 500);
            $table->string('hash_arquivo', 64)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('ppa_audiencia_publica_id')
                ->references('id')
                ->on('ppa_audiencias_publicas')
                ->onDelete('cascade');

            $table->index(['ppa_audiencia_publica_id'], 'ppa_aud_pub_anexo_idx_audiencia');
            $table->index(['hash_arquivo'], 'ppa_aud_pub_anexo_idx_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppa_audiencias_publicas_anexos');
        Schema::dropIfExists('ppa_audiencias_publicas');
    }
}
