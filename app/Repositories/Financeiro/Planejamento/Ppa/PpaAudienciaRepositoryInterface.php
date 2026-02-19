<?php

namespace App\Repositories\Financeiro\Planejamento\Ppa;

interface PpaAudienciaRepositoryInterface
{
    /**
     * @param array<string, mixed> $dados
     * @return array<string, mixed>
     */
    public function criarAudiencia(array $dados): array;

    /**
     * @return array<string, mixed>|null
     */
    public function obterAudienciaPorId(int $audienciaId): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarAudienciasPorVersao(int $versaoId): array;

    /**
     * @param array<string, mixed> $dados
     * @return array<string, mixed>
     */
    public function criarAnexo(array $dados): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarAnexosPorAudiencia(int $audienciaId): array;

    /**
     * @return array<string, mixed>|null
     */
    public function obterAnexoPorId(int $anexoId): ?array;
}
