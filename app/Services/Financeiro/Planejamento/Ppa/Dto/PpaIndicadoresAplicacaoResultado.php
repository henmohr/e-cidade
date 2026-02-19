<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaIndicadoresAplicacaoResultado
{
    public int $versaoId;
    /** @var array<int, int> */
    public array $exercicios;
    public float $baseReceita;
    /** @var array<string, array<string, float>> */
    public array $indicadores;

    /**
     * @param array<int, int> $exercicios
     * @param array<string, array<string, float>> $indicadores
     */
    public function __construct(int $versaoId, array $exercicios, float $baseReceita, array $indicadores)
    {
        $this->versaoId = $versaoId;
        $this->exercicios = $exercicios;
        $this->baseReceita = $baseReceita;
        $this->indicadores = $indicadores;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'exercicios' => $this->exercicios,
            'base_receita' => $this->baseReceita,
            'indicadores' => $this->indicadores,
        ];
    }
}
