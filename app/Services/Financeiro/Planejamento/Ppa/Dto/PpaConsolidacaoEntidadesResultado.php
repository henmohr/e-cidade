<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaConsolidacaoEntidadesResultado
{
    /** @var array<int, int> */
    public array $versoes;
    /** @var array<int, int> */
    public array $entidades;
    public ?string $ateData;
    public float $totalReceita;
    public float $totalDespesa;
    public float $totalAlteracoesReceita;
    public float $totalTransferenciasFinanceiras;
    /** @var array<int, array<string, mixed>> */
    public array $entidadesConsolidadas;

    /**
     * @param array<int, int> $versoes
     * @param array<int, int> $entidades
     * @param array<int, array<string, mixed>> $entidadesConsolidadas
     */
    public function __construct(
        array $versoes,
        array $entidades,
        ?string $ateData,
        float $totalReceita,
        float $totalDespesa,
        float $totalAlteracoesReceita,
        float $totalTransferenciasFinanceiras,
        array $entidadesConsolidadas
    ) {
        $this->versoes = $versoes;
        $this->entidades = $entidades;
        $this->ateData = $ateData;
        $this->totalReceita = $totalReceita;
        $this->totalDespesa = $totalDespesa;
        $this->totalAlteracoesReceita = $totalAlteracoesReceita;
        $this->totalTransferenciasFinanceiras = $totalTransferenciasFinanceiras;
        $this->entidadesConsolidadas = $entidadesConsolidadas;
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
            'total_receita' => $this->totalReceita,
            'total_despesa' => $this->totalDespesa,
            'total_alteracoes_receita' => $this->totalAlteracoesReceita,
            'total_transferencias_financeiras' => $this->totalTransferenciasFinanceiras,
            'entidades_consolidadas' => $this->entidadesConsolidadas,
        ];
    }
}
