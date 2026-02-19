<?php

namespace App\Http\Requests\Financeiro\Planejamento;

use Illuminate\Foundation\Http\FormRequest;

class StorePpaAudienciaPublicaAnexoRequest extends FormRequest
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
            'nome_arquivo' => ['required', 'string', 'max:255'],
            'conteudo_base64' => ['required', 'string'],
            'mime_type' => ['sometimes', 'nullable', 'string', 'max:120'],
        ];
    }
}
