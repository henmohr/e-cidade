<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaCadastroResultado
{
    public int $planoId;
    public string $codigo;
    public string $status;
    public ?int $versaoInicialId;
    public string $mensagem;

    public function __construct(
        int $planoId,
        string $codigo,
        string $status,
        ?int $versaoInicialId,
        string $mensagem
    ) {
        $this->planoId = $planoId;
        $this->codigo = $codigo;
        $this->status = $status;
        $this->versaoInicialId = $versaoInicialId;
        $this->mensagem = $mensagem;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'plano_id' => $this->planoId,
            'codigo' => $this->codigo,
            'status' => $this->status,
            'versao_inicial_id' => $this->versaoInicialId,
            'mensagem' => $this->mensagem,
        ];
    }
}
