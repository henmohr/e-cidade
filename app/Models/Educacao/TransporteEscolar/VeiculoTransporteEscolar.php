<?php

namespace App\Models\Educacao\TransporteEscolar;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VeiculoTransporteEscolar extends LegacyModel
{
    protected $table = 'educacao_transporte_escolar_veiculos';

    protected $fillable = [
        'placa',
        'modelo',
        'motorista_nome',
        'capacidade',
        'situacao',
        'observacao',
    ];

    protected $casts = [
        'capacidade' => 'integer',
    ];

    public function linhas(): HasMany
    {
        return $this->hasMany(LinhaVeiculoTransporteEscolar::class, 'veiculo_id');
    }
}
