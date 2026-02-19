<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaValidacaoResultado
{
    public bool $valido;
    /** @var array<int, array<string, mixed>> */
    public array $erros;

    /**
     * @param array<int, array<string, mixed>> $erros
     */
    public function __construct(bool $valido, array $erros)
    {
        $this->valido = $valido;
        $this->erros = $erros;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'valido' => $this->valido,
            'erros' => $this->erros,
        ];
    }
}
