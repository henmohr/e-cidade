<?php

namespace App\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\DemonstracaoRepository;
use App\Repositories\Financeiro\Relatorio\DemonstracaoRepositoryInterface;
use InvalidArgumentException;

class DemonstracaoContabilService
{
    public const TIPO_DVP = 'dvp';
    public const TIPO_DFC = 'dfc';
    public const TIPO_TODAS = 'todas';

    private DemonstracaoRepositoryInterface $repository;

    public function __construct(?DemonstracaoRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new DemonstracaoRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function gerar(string $tipo = self::TIPO_TODAS, ?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $tipoNormalizado = strtolower(trim($tipo));
        $this->assertTipoValido($tipoNormalizado);

        $demonstracoes = [];

        if (in_array($tipoNormalizado, [self::TIPO_TODAS, self::TIPO_DVP], true)) {
            $demonstracoes[self::TIPO_DVP] = $this->repository->obterDadosDvp($dataInicial, $dataFinal);
        }

        if (in_array($tipoNormalizado, [self::TIPO_TODAS, self::TIPO_DFC], true)) {
            $demonstracoes[self::TIPO_DFC] = $this->repository->obterDadosDfc($dataInicial, $dataFinal);
        }

        return [
            'tipo' => $tipoNormalizado,
            'periodo' => [
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
            'gerado_em' => date('c'),
            'demonstracoes' => $demonstracoes,
        ];
    }

    private function assertTipoValido(string $tipo): void
    {
        if (!in_array($tipo, [self::TIPO_DVP, self::TIPO_DFC, self::TIPO_TODAS], true)) {
            throw new InvalidArgumentException('Tipo de demonstracao invalido: ' . $tipo);
        }
    }
}
