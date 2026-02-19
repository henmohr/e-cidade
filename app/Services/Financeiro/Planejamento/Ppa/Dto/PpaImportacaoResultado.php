<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaImportacaoResultado
{
    public int $versaoOrigemId;
    public int $versaoDestinoId;
    /** @var array<string, int> */
    public array $totais;
    public string $mensagem;
    /** @var array<string, mixed> */
    public array $detalhes;

    /**
     * @param array<string, int> $totais
     * @param array<string, mixed> $detalhes
     */
    public function __construct(
        int $versaoOrigemId,
        int $versaoDestinoId,
        array $totais,
        string $mensagem,
        array $detalhes = []
    ) {
        $this->versaoOrigemId = $versaoOrigemId;
        $this->versaoDestinoId = $versaoDestinoId;
        $this->totais = $totais;
        $this->mensagem = $mensagem;
        $this->detalhes = $detalhes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_origem_id' => $this->versaoOrigemId,
            'versao_destino_id' => $this->versaoDestinoId,
            'totais' => $this->totais,
            'mensagem' => $this->mensagem,
            'detalhes' => $this->detalhes,
        ];
    }
}
