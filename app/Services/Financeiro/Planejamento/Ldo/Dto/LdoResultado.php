<?php

namespace App\Services\Financeiro\Planejamento\Ldo\Dto;

class LdoResultado
{
    public int $id;
    public string $mensagem;
    /** @var array<string,mixed> */
    public array $dados;

    /** @param array<string,mixed> $dados */
    public function __construct(int $id, string $mensagem, array $dados)
    {
        $this->id = $id;
        $this->mensagem = $mensagem;
        $this->dados = $dados;
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'mensagem' => $this->mensagem,
            'dados' => $this->dados,
        ];
    }
}
