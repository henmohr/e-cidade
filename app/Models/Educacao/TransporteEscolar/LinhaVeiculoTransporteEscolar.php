<?php

namespace App\Models\Educacao\TransporteEscolar;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinhaVeiculoTransporteEscolar extends LegacyModel
{
    protected $table = 'educacao_transporte_escolar_linha_veiculos';

    protected $fillable = [
        'linha_id',
        'veiculo_id',
        'data_inicio',
        'data_fim',
        'observacao',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function linha(): BelongsTo
    {
        return $this->belongsTo(LinhaTransporteEscolar::class, 'linha_id');
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(VeiculoTransporteEscolar::class, 'veiculo_id');
    }
}
