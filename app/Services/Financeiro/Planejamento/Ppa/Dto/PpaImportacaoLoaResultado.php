<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaImportacaoLoaResultado
{
    public int $versaoDestinoId;
    public int $exercicioLoa;
    /** @var array<string, int> */
    public array $totais;
    public string $mensagem;

    /**
     * @param array<string, int> $totais
     */
    public function __construct(int $versaoDestinoId, int $exercicioLoa, array $totais, string $mensagem)
    {
        $this->versaoDestinoId = $versaoDestinoId;
        $this->exercicioLoa = $exercicioLoa;
        $this->totais = $totais;
        $this->mensagem = $mensagem;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_destino_id' => $this->versaoDestinoId,
            'exercicio_loa' => $this->exercicioLoa,
            'totais' => $this->totais,
            'mensagem' => $this->mensagem,
        ];
    }
}
