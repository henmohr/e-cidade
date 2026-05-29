<?php

namespace App\Models\Educacao\TransporteEscolar;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LinhaTransporteEscolar extends LegacyModel
{
    protected $table = 'educacao_transporte_escolar_linhas';

    protected $fillable = [
        'codigo',
        'nome',
        'tipo_servico',
        'horario_saida',
        'horario_retorno',
        'custo_mensal',
        'unidade_escolar',
        'rota_descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'bool',
        'custo_mensal' => 'decimal:2',
    ];

    public function veiculos(): HasMany
    {
        return $this->hasMany(LinhaVeiculoTransporteEscolar::class, 'linha_id');
    }

    public function alunos(): HasMany
    {
        return $this->hasMany(AlunoTransporteEscolar::class, 'linha_id');
    }

    public function pontos(): HasMany
    {
        return $this->hasMany(PontoTransporteEscolar::class, 'linha_id')->orderBy('ordem');
    }
}
