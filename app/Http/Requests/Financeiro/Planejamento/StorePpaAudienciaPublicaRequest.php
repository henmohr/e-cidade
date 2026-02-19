<?php

namespace App\Http\Requests\Financeiro\Planejamento;

use Illuminate\Foundation\Http\FormRequest;

class StorePpaAudienciaPublicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'entidade_id' => ['sometimes', 'integer', 'min:1'],
            'data_realizacao' => ['required', 'date_format:Y-m-d'],
            'solicitacoes_comunidade' => ['required', 'string'],
            'bairro_atendido' => ['required', 'string', 'max:120'],
            'contato_solicitante' => ['required', 'string', 'max:255'],
            'orgao_responsavel' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:recebida,em_analise,deferida,indeferida,concluida'],
            'observacao' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
