<?php

namespace App\Repositories\Financeiro\Planejamento\Ppa;

use App\Models\Financeiro\Planejamento\PpaAudienciaPublica;
use App\Models\Financeiro\Planejamento\PpaAudienciaPublicaAnexo;

class PpaAudienciaRepository implements PpaAudienciaRepositoryInterface
{
    public function criarAudiencia(array $dados): array
    {
        /** @var PpaAudienciaPublica $audiencia */
        $audiencia = PpaAudienciaPublica::query()->create($dados);
        return $audiencia->toArray();
    }

    public function obterAudienciaPorId(int $audienciaId): ?array
    {
        $audiencia = PpaAudienciaPublica::query()->find($audienciaId);
        return $audiencia ? $audiencia->toArray() : null;
    }

    public function listarAudienciasPorVersao(int $versaoId): array
    {
        return PpaAudienciaPublica::query()
            ->where('ppa_versao_id', $versaoId)
            ->orderByDesc('data_realizacao')
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }

    public function criarAnexo(array $dados): array
    {
        /** @var PpaAudienciaPublicaAnexo $anexo */
        $anexo = PpaAudienciaPublicaAnexo::query()->create($dados);
        return $anexo->toArray();
    }

    public function listarAnexosPorAudiencia(int $audienciaId): array
    {
        return PpaAudienciaPublicaAnexo::query()
            ->where('ppa_audiencia_publica_id', $audienciaId)
            ->orderByDesc('id')
            ->get()
            ->toArray();
    }

    public function obterAnexoPorId(int $anexoId): ?array
    {
        $anexo = PpaAudienciaPublicaAnexo::query()->find($anexoId);
        return $anexo ? $anexo->toArray() : null;
    }
}
