<?php

namespace App\Repositories\Financeiro\Integracao;

interface PortalTransparenciaPublicacaoRepositoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function obterDadosReceitas(?string $dataReferencia = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function obterDadosDespesas(?string $dataReferencia = null): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function obterDadosContratos(?string $dataReferencia = null): array;
}
