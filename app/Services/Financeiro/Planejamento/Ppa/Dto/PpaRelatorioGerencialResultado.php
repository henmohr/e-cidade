<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaRelatorioGerencialResultado
{
    /** @var array<int, int> */
    public array $versoes;
    /** @var array<int, int>|null */
    public ?array $entidades;
    public ?string $ateData;
    public float $totalReceitas;
    public float $totalDespesas;
    public float $totalTransferencias;
    /** @var array<int, array<string, mixed>> */
    public array $receitasPorFonte;
    /** @var array<int, array<string, mixed>> */
    public array $despesasPorFonte;
    /** @var array<int, array<string, mixed>> */
    public array $transferencias;

    /**
     * @param array<int, int> $versoes
     * @param array<int, int>|null $entidades
     * @param array<int, array<string, mixed>> $receitasPorFonte
     * @param array<int, array<string, mixed>> $despesasPorFonte
     * @param array<int, array<string, mixed>> $transferencias
     */
    public function __construct(
        array $versoes,
        ?array $entidades,
        ?string $ateData,
        float $totalReceitas,
        float $totalDespesas,
        float $totalTransferencias,
        array $receitasPorFonte,
        array $despesasPorFonte,
        array $transferencias
    ) {
        $this->versoes = $versoes;
        $this->entidades = $entidades;
        $this->ateData = $ateData;
        $this->totalReceitas = $totalReceitas;
        $this->totalDespesas = $totalDespesas;
        $this->totalTransferencias = $totalTransferencias;
        $this->receitasPorFonte = $receitasPorFonte;
        $this->despesasPorFonte = $despesasPorFonte;
        $this->transferencias = $transferencias;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versoes' => $this->versoes,
            'entidades' => $this->entidades,
            'ate_data' => $this->ateData,
            'totais' => [
                'receitas' => $this->totalReceitas,
                'despesas' => $this->totalDespesas,
                'transferencias' => $this->totalTransferencias,
            ],
            'receitas_por_fonte' => $this->receitasPorFonte,
            'despesas_por_fonte' => $this->despesasPorFonte,
            'transferencias' => $this->transferencias,
        ];
    }
}
