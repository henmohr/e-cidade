<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaCompatibilizacaoResultado
{
    public int $versaoPpaId;
    public int $exercicioLdo;
    public int $exercicioLoa;
    /** @var array<int, array<string, mixed>> */
    public array $receitas;
    /** @var array<int, array<string, mixed>> */
    public array $metasDespesa;

    /**
     * @param array<int, array<string, mixed>> $receitas
     * @param array<int, array<string, mixed>> $metasDespesa
     */
    public function __construct(
        int $versaoPpaId,
        int $exercicioLdo,
        int $exercicioLoa,
        array $receitas,
        array $metasDespesa
    ) {
        $this->versaoPpaId = $versaoPpaId;
        $this->exercicioLdo = $exercicioLdo;
        $this->exercicioLoa = $exercicioLoa;
        $this->receitas = $receitas;
        $this->metasDespesa = $metasDespesa;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_ppa_id' => $this->versaoPpaId,
            'exercicio_ldo' => $this->exercicioLdo,
            'exercicio_loa' => $this->exercicioLoa,
            'receitas' => $this->receitas,
            'metas_despesa' => $this->metasDespesa,
        ];
    }
}
