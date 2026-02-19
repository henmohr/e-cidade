<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaProjecaoResultado
{
    public int $versaoId;
    /** @var array<int, array<string, float|int>> */
    public array $anos;
    public float $totalReceita;
    public float $totalDespesa;
    public float $saldoTotal;

    /**
     * @param array<int, array<string, float|int>> $anos
     */
    public function __construct(
        int $versaoId,
        array $anos,
        float $totalReceita,
        float $totalDespesa,
        float $saldoTotal
    ) {
        $this->versaoId = $versaoId;
        $this->anos = $anos;
        $this->totalReceita = $totalReceita;
        $this->totalDespesa = $totalDespesa;
        $this->saldoTotal = $saldoTotal;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'anos' => $this->anos,
            'total_receita' => $this->totalReceita,
            'total_despesa' => $this->totalDespesa,
            'saldo_total' => $this->saldoTotal,
        ];
    }
}
