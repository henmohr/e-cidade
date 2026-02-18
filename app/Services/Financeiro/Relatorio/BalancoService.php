<?php

namespace App\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\BalancoRepository;
use App\Repositories\Financeiro\Relatorio\BalancoRepositoryInterface;
use InvalidArgumentException;

class BalancoService
{
    public const TIPO_PATRIMONIAL = 'patrimonial';
    public const TIPO_ORCAMENTARIO = 'orcamentario';
    public const TIPO_FINANCEIRO = 'financeiro';
    public const TIPO_TODOS = 'todos';

    private BalancoRepositoryInterface $repository;

    public function __construct(?BalancoRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new BalancoRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function gerar(string $tipo = self::TIPO_TODOS, ?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $tipoNormalizado = strtolower(trim($tipo));
        $this->assertTipoValido($tipoNormalizado);

        $relatorios = [];

        if (in_array($tipoNormalizado, [self::TIPO_TODOS, self::TIPO_PATRIMONIAL], true)) {
            $relatorios[self::TIPO_PATRIMONIAL] = $this->repository->obterDadosBalancoPatrimonial($dataInicial, $dataFinal);
        }

        if (in_array($tipoNormalizado, [self::TIPO_TODOS, self::TIPO_ORCAMENTARIO], true)) {
            $relatorios[self::TIPO_ORCAMENTARIO] = $this->repository->obterDadosBalancoOrcamentario($dataInicial, $dataFinal);
        }

        if (in_array($tipoNormalizado, [self::TIPO_TODOS, self::TIPO_FINANCEIRO], true)) {
            $relatorios[self::TIPO_FINANCEIRO] = $this->repository->obterDadosBalancoFinanceiro($dataInicial, $dataFinal);
        }

        return [
            'tipo' => $tipoNormalizado,
            'periodo' => [
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
            'gerado_em' => date('c'),
            'relatorios' => $relatorios,
        ];
    }

    private function assertTipoValido(string $tipo): void
    {
        $tiposPermitidos = [
            self::TIPO_PATRIMONIAL,
            self::TIPO_ORCAMENTARIO,
            self::TIPO_FINANCEIRO,
            self::TIPO_TODOS,
        ];

        if (!in_array($tipo, $tiposPermitidos, true)) {
            throw new InvalidArgumentException('Tipo de balanco invalido: ' . $tipo);
        }
    }
}
