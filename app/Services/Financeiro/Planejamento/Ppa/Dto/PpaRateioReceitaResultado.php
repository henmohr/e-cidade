<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaRateioReceitaResultado
{
    public int $versaoId;
    public string $contaReceita;
    public int $exercicio;
    public float $valorTotal;
    /** @var array<int, array<string, mixed>> */
    public array $receitasCriadas;
    public string $mensagem;

    /**
     * @param array<int, array<string, mixed>> $receitasCriadas
     */
    public function __construct(
        int $versaoId,
        string $contaReceita,
        int $exercicio,
        float $valorTotal,
        array $receitasCriadas,
        string $mensagem
    ) {
        $this->versaoId = $versaoId;
        $this->contaReceita = $contaReceita;
        $this->exercicio = $exercicio;
        $this->valorTotal = $valorTotal;
        $this->receitasCriadas = $receitasCriadas;
        $this->mensagem = $mensagem;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'conta_receita' => $this->contaReceita,
            'exercicio' => $this->exercicio,
            'valor_total' => $this->valorTotal,
            'receitas_criadas' => $this->receitasCriadas,
            'mensagem' => $this->mensagem,
        ];
    }
}
