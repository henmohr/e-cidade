<?php

namespace App\Services\Financeiro\Planejamento\Ppa\Dto;

class PpaRelatorioObrigatorioResultado
{
    public string $tipo;
    public string $titulo;
    /** @var array<int, int> */
    public array $versoes;
    /** @var array<int, int>|null */
    public ?array $entidades;
    public ?string $ateData;
    /** @var array<int, array<string, mixed>> */
    public array $linhas;

    /**
     * @param array<int, int> $versoes
     * @param array<int, int>|null $entidades
     * @param array<int, array<string, mixed>> $linhas
     */
    public function __construct(
        string $tipo,
        string $titulo,
        array $versoes,
        ?array $entidades,
        ?string $ateData,
        array $linhas
    ) {
        $this->tipo = $tipo;
        $this->titulo = $titulo;
        $this->versoes = $versoes;
        $this->entidades = $entidades;
        $this->ateData = $ateData;
        $this->linhas = $linhas;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'versoes' => $this->versoes,
            'entidades' => $this->entidades,
            'ate_data' => $this->ateData,
            'linhas' => $this->linhas,
        ];
    }
}
