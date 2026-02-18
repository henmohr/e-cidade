<?php

namespace App\Services\Financeiro\Relatorio;

use App\Repositories\Financeiro\Relatorio\RelatorioFiscalRepository;
use App\Repositories\Financeiro\Relatorio\RelatorioFiscalRepositoryInterface;
use InvalidArgumentException;

class RelatorioFiscalService
{
    public const TIPO_RGF = 'rgf';
    public const TIPO_RREO = 'rreo';
    public const TIPO_TODOS = 'todos';

    public const PERIODICIDADE_MENSAL = 'mensal';
    public const PERIODICIDADE_QUADRIMESTRAL = 'quadrimestral';

    private RelatorioFiscalRepositoryInterface $repository;

    public function __construct(?RelatorioFiscalRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new RelatorioFiscalRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function gerar(
        string $tipo = self::TIPO_TODOS,
        string $periodicidade = self::PERIODICIDADE_QUADRIMESTRAL,
        ?string $dataInicial = null,
        ?string $dataFinal = null
    ): array {
        $tipoNormalizado = strtolower(trim($tipo));
        $periodicidadeNormalizada = strtolower(trim($periodicidade));

        $this->assertTipoValido($tipoNormalizado);
        $this->assertPeriodicidadeValida($periodicidadeNormalizada);

        $relatorios = [];

        if (in_array($tipoNormalizado, [self::TIPO_TODOS, self::TIPO_RGF], true)) {
            $relatorios[self::TIPO_RGF] = $this->repository->obterDadosRgf($dataInicial, $dataFinal);
        }

        if (in_array($tipoNormalizado, [self::TIPO_TODOS, self::TIPO_RREO], true)) {
            $relatorios[self::TIPO_RREO] = $this->repository->obterDadosRreo($dataInicial, $dataFinal);
        }

        return [
            'tipo' => $tipoNormalizado,
            'periodicidade' => $periodicidadeNormalizada,
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
        if (!in_array($tipo, [self::TIPO_RGF, self::TIPO_RREO, self::TIPO_TODOS], true)) {
            throw new InvalidArgumentException('Tipo de relatorio fiscal invalido: ' . $tipo);
        }
    }

    private function assertPeriodicidadeValida(string $periodicidade): void
    {
        if (!in_array($periodicidade, [self::PERIODICIDADE_MENSAL, self::PERIODICIDADE_QUADRIMESTRAL], true)) {
            throw new InvalidArgumentException('Periodicidade invalida: ' . $periodicidade);
        }
    }
}
