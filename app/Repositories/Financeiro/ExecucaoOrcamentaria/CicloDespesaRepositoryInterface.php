<?php

namespace App\Repositories\Financeiro\ExecucaoOrcamentaria;

interface CicloDespesaRepositoryInterface
{
    public function getNumeroEmpenhoPorOrdemPagamento(int $codigoOrdem): ?int;

    public function hasFixacao(int $ano, int $codigoDotacao): bool;

    public function hasFixacaoParaEmpenho(int $numeroEmpenho): bool;

    public function getSaldoDisponivelDotacao(int $ano, int $codigoDotacao): ?float;

    public function hasEmpenho(int $numeroEmpenho): bool;

    public function hasLiquidacao(int $numeroEmpenho): bool;

    public function hasPagamento(int $numeroEmpenho): bool;
}
