<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaConfrontoReceitaDespesaResultado
{
    /** @var array<int, int> */
    public array $versoes;
    public ?string $ateData;
    public float $totalReceita;
    public float $totalDespesa;
    public float $saldoTotal;
    /** @var array<int, array<string, mixed>> */
    public array $fontes;

    /**
     * @param array<int, int> $versoes
     * @param array<int, array<string, mixed>> $fontes
     */
    public function __construct(
        array $versoes,
        ?string $ateData,
        float $totalReceita,
        float $totalDespesa,
        float $saldoTotal,
        array $fontes
    ) {
        $this->versoes = $versoes;
        $this->ateData = $ateData;
        $this->totalReceita = $totalReceita;
        $this->totalDespesa = $totalDespesa;
        $this->saldoTotal = $saldoTotal;
        $this->fontes = $fontes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versoes' => $this->versoes,
            'ate_data' => $this->ateData,
            'total_receita' => $this->totalReceita,
            'total_despesa' => $this->totalDespesa,
            'saldo_total' => $this->saldoTotal,
            'fontes' => $this->fontes,
        ];
    }
}
