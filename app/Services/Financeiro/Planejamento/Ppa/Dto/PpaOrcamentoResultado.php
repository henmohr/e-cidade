<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaOrcamentoResultado
{
    public int $versaoId;
    public ?string $ateData;
    public float $totalReceita;
    public float $totalDespesa;
    public float $saldo;
    /** @var array<int, array<string, mixed>> */
    public array $receitas;
    /** @var array<int, array<string, mixed>> */
    public array $despesas;

    /**
     * @param array<int, array<string, mixed>> $receitas
     * @param array<int, array<string, mixed>> $despesas
     */
    public function __construct(
        int $versaoId,
        ?string $ateData,
        float $totalReceita,
        float $totalDespesa,
        float $saldo,
        array $receitas,
        array $despesas
    ) {
        $this->versaoId = $versaoId;
        $this->ateData = $ateData;
        $this->totalReceita = $totalReceita;
        $this->totalDespesa = $totalDespesa;
        $this->saldo = $saldo;
        $this->receitas = $receitas;
        $this->despesas = $despesas;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'ate_data' => $this->ateData,
            'total_receita' => $this->totalReceita,
            'total_despesa' => $this->totalDespesa,
            'saldo' => $this->saldo,
            'receitas' => $this->receitas,
            'despesas' => $this->despesas,
        ];
    }
}
