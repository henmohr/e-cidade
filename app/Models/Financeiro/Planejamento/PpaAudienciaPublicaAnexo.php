<?php

namespace App\Models\Financeiro\Planejamento;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpaAudienciaPublicaAnexo extends LegacyModel
{
    protected $table = 'ppa_audiencias_publicas_anexos';

    protected $fillable = [
        'ppa_audiencia_publica_id',
        'nome_original',
        'nome_arquivo',
        'mime_type',
        'tamanho_bytes',
        'storage_disk',
        'storage_path',
        'hash_arquivo',
        'created_by',
        'updated_by',
    ];

    public function audiencia(): BelongsTo
    {
        return $this->belongsTo(PpaAudienciaPublica::class, 'ppa_audiencia_publica_id');
    }
}
