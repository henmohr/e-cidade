<?php

namespace App\Repositories\Financeiro\Integracao;

interface IntegracaoGovernamentalRepositoryInterface
{
    public function criarRegistro(string $sistema, string $referencia, string $status, array $payload = []): int;

    public function atualizarRegistro(
        int $codigo,
        string $status,
        ?string $protocoloExterno = null,
        ?string $mensagem = null
    ): void;

    /**
     * @param array<int, string> $status
     * @return array<int, array<string, mixed>>
     */
    public function buscarPorStatus(array $status, ?string $sistema = null, int $limite = 100): array;

    public function incrementarTentativaReprocessamento(int $codigo): void;

    /**
     * @return array<string, mixed>|null
     */
    public function buscarPorCodigo(int $codigo): ?array;
}
