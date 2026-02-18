<?php

namespace App\Services\Financeiro\Receita;

use App\Repositories\Financeiro\Receita\ClassificacaoReceitaRepository;
use App\Repositories\Financeiro\Receita\ClassificacaoReceitaRepositoryInterface;

class ClassificacaoReceitaService
{
    private ClassificacaoReceitaRepositoryInterface $repository;

    public function __construct(?ClassificacaoReceitaRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new ClassificacaoReceitaRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function classificarPorCodigo(int $codigoReceita): array
    {
        $receita = $this->repository->obterReceitaPorCodigo($codigoReceita);
        if ($receita === null) {
            return [
                'codigo_receita' => $codigoReceita,
                'status' => 'NAO_ENCONTRADA',
                'grupo' => null,
                'subgrupo' => null,
                'descricao' => null,
            ];
        }

        $natureza = $this->extrairNatureza((string) $receita['k02_codigo']);
        $classificacao = $this->mapearClassificacao($natureza);

        return [
            'codigo_receita' => (int) $receita['k02_codigo'],
            'status' => $classificacao['status'],
            'grupo' => $classificacao['grupo'],
            'subgrupo' => $classificacao['subgrupo'],
            'natureza' => $natureza,
            'descricao' => (string) ($receita['k02_drecei'] ?: $receita['k02_descr']),
        ];
    }

    private function extrairNatureza(string $codigoReceita): string
    {
        $numeros = preg_replace('/\D+/', '', $codigoReceita) ?? '';
        return $numeros !== '' ? substr($numeros, 0, 1) : '';
    }

    /**
     * @return array<string, string|null>
     */
    private function mapearClassificacao(string $natureza): array
    {
        if ($natureza === '1') {
            return [
                'status' => 'CLASSIFICADA',
                'grupo' => 'RECEITAS_CORRENTES',
                'subgrupo' => 'ORCAMENTARIA_PRIMARIA',
            ];
        }

        if ($natureza === '2') {
            return [
                'status' => 'CLASSIFICADA',
                'grupo' => 'RECEITAS_DE_CAPITAL',
                'subgrupo' => 'ORCAMENTARIA_PRIMARIA',
            ];
        }

        if ($natureza === '7') {
            return [
                'status' => 'CLASSIFICADA',
                'grupo' => 'RECEITAS_CORRENTES',
                'subgrupo' => 'INTRAORCAMENTARIA',
            ];
        }

        if ($natureza === '8') {
            return [
                'status' => 'CLASSIFICADA',
                'grupo' => 'RECEITAS_DE_CAPITAL',
                'subgrupo' => 'INTRAORCAMENTARIA',
            ];
        }

        return [
            'status' => 'PENDENTE_CLASSIFICACAO',
            'grupo' => null,
            'subgrupo' => null,
        ];
    }
}

