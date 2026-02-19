<?php

namespace App\Models\Financeiro\Planejamento;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpaVersao extends LegacyModel
{
    protected $table = 'ppa_versoes';

    protected $fillable = [
        'ppa_plano_id',
        'numero_versao',
        'status',
        'motivo',
        'publicado_em',
        'publicado_by',
        'created_by',
        'updated_by',
    ];

    public function plano(): BelongsTo
    {
        return $this->belongsTo(PpaPlano::class, 'ppa_plano_id');
    }

    public function programas(): HasMany
    {
        return $this->hasMany(PpaPrograma::class, 'ppa_versao_id');
    }

    public function receitasProgramadas(): HasMany
    {
        return $this->hasMany(PpaReceitaProgramada::class, 'ppa_versao_id');
    }

    public function metasDespesa(): HasMany
    {
        return $this->hasMany(PpaMetaDespesa::class, 'ppa_versao_id');
    }

    public function vinculosTce(): HasMany
    {
        return $this->hasMany(PpaVinculoTce::class, 'ppa_versao_id');
    }

    public function audienciasPublicas(): HasMany
    {
        return $this->hasMany(PpaAudienciaPublica::class, 'ppa_versao_id');
    }
}
