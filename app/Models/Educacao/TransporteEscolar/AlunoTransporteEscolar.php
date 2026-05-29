<?php

namespace App\Models\Educacao\TransporteEscolar;

use App\Models\LegacyModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlunoTransporteEscolar extends LegacyModel
{
    protected $table = 'educacao_transporte_escolar_alunos';

    protected $fillable = [
        'linha_id',
        'cgm_id',
        'aluno_nome',
        'aluno_cpf',
        'escola_nome',
        'local_embarque',
        'motivo_uso',
        'periodo_uso',
        'situacao_matricula',
        'foto_path',
        'utiliza_transporte',
    ];

    protected $casts = [
        'utiliza_transporte' => 'bool',
    ];

    public function linha(): BelongsTo
    {
        return $this->belongsTo(LinhaTransporteEscolar::class, 'linha_id');
    }
}
