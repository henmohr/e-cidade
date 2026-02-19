<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaAudienciaPublicaResultado
{
    public int $versaoId;
    public string $mensagem;
    /** @var array<string, mixed> */
    public array $dados;
    public ?int $totalRegistros;

    /**
     * @param array<string, mixed> $dados
     */
    public function __construct(int $versaoId, string $mensagem, array $dados, ?int $totalRegistros = null)
    {
        $this->versaoId = $versaoId;
        $this->mensagem = $mensagem;
        $this->dados = $dados;
        $this->totalRegistros = $totalRegistros;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'mensagem' => $this->mensagem,
            'total_registros' => $this->totalRegistros,
            'dados' => $this->dados,
        ];
    }
}
