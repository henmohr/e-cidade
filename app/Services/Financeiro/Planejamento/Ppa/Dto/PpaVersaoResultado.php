<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaVersaoResultado
{
    public int $versaoId;
    public int $planoId;
    public int $numeroVersao;
    public string $status;
    public string $mensagem;
    public ?string $publicadoEm;
    /** @var array<string, mixed> */
    public array $dados;

    /**
     * @param array<string, mixed> $dados
     */
    public function __construct(
        int $versaoId,
        int $planoId,
        int $numeroVersao,
        string $status,
        string $mensagem,
        ?string $publicadoEm,
        array $dados
    ) {
        $this->versaoId = $versaoId;
        $this->planoId = $planoId;
        $this->numeroVersao = $numeroVersao;
        $this->status = $status;
        $this->mensagem = $mensagem;
        $this->publicadoEm = $publicadoEm;
        $this->dados = $dados;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'versao_id' => $this->versaoId,
            'plano_id' => $this->planoId,
            'numero_versao' => $this->numeroVersao,
            'status' => $this->status,
            'mensagem' => $this->mensagem,
            'publicado_em' => $this->publicadoEm,
            'dados' => $this->dados,
        ];
    }
}
