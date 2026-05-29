<?php

namespace App\Models\Educacao\TransporteEscolar;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PontoTransporteEscolar extends LegacyModel
{
    protected $table = 'educacao_transporte_escolar_pontos';

    protected $fillable = [
        'linha_id',
        'nome',
        'endereco',
        'tipo_ponto',
        'ordem',
        'observacao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'bool',
        'ordem' => 'integer',
    ];

    public function linha(): BelongsTo
    {
        return $this->belongsTo(LinhaTransporteEscolar::class, 'linha_id');
    }
}
