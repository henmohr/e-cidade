<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaAvaliacaoResultadosResultado
{
    public int $versaoId;
    /** @var array<int, int> */
    public array $exercicios;
    /** @var array<int, array<string, mixed>> */
    public array $linhas;

    /**
     * @param array<int, int> $exercicios
     * @param array<int, array<string, mixed>> $linhas
     */
    public function __construct(int $versaoId, array $exercicios, array $linhas)
    {
        $this->versaoId = $versaoId;
        $this->exercicios = $exercicios;
        $this->linhas = $linhas;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'exercicios' => $this->exercicios,
            'linhas' => $this->linhas,
        ];
    }
}
