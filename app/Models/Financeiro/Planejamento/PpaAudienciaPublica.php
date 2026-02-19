<?php

namespace App\Models\Financeiro\Planejamento;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpaAudienciaPublica extends LegacyModel
{
    protected $table = 'ppa_audiencias_publicas';

    protected $fillable = [
        'ppa_versao_id',
        'entidade_id',
        'data_realizacao',
        'solicitacoes_comunidade',
        'bairro_atendido',
        'contato_solicitante',
        'orgao_responsavel',
        'status',
        'observacao',
        'created_by',
        'updated_by',
    ];

    public function versao(): BelongsTo
    {
        return $this->belongsTo(PpaVersao::class, 'ppa_versao_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(PpaAudienciaPublicaAnexo::class, 'ppa_audiencia_publica_id');
    }
}
