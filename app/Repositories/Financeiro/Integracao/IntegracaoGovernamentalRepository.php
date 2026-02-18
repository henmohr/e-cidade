<?php

namespace App\Repositories\Financeiro\Integracao;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class IntegracaoGovernamentalRepository implements IntegracaoGovernamentalRepositoryInterface
{
    public function criarRegistro(string $sistema, string $referencia, string $status, array $payload = []): int
    {
        $dados = [
            'igs01_sistema' => $sistema,
            'igs01_referencia' => $referencia,
            'igs01_status' => $status,
            'igs01_payload' => json_encode($payload),
            'igs01_tentativas_reprocessamento' => 0,
            'igs01_criado_em' => now(),
            'igs01_atualizado_em' => now(),
        ];

        try {
            return (int) DB::table('integracao.integracao_envio_status')->insertGetId($dados, 'igs01_codigo');
        } catch (QueryException $exception) {
            return (int) DB::table('integracao_envio_status')->insertGetId($dados, 'igs01_codigo');
        }
    }

    public function atualizarRegistro(
        int $codigo,
        string $status,
        ?string $protocoloExterno = null,
        ?string $mensagem = null
    ): void {
        $dados = [
            'igs01_status' => $status,
            'igs01_protocolo_externo' => $protocoloExterno,
            'igs01_mensagem' => $mensagem,
            'igs01_atualizado_em' => now(),
        ];

        $this->runUpdateWithFallback($codigo, $dados);
    }

    public function buscarPorStatus(array $status, ?string $sistema = null, int $limite = 100): array
    {
        $consulta = function (string $tabela) use ($status, $sistema, $limite): array {
            $query = DB::table($tabela)
                ->select([
                    'igs01_codigo as codigo',
                    'igs01_sistema as sistema',
                    'igs01_referencia as referencia',
                    'igs01_status as status',
                    'igs01_payload as payload',
                    'igs01_protocolo_externo as protocolo_externo',
                    'igs01_mensagem as mensagem',
                    'igs01_tentativas_reprocessamento as tentativas_reprocessamento',
                    'igs01_atualizado_em as atualizado_em',
                ])
                ->whereIn('igs01_status', $status)
                ->limit($limite)
                ->orderBy('igs01_atualizado_em');

            if ($sistema !== null && $sistema !== '') {
                $query->where('igs01_sistema', $sistema);
            }

            return $query->get()->map(static function ($registro): array {
                return [
                    'codigo' => (int) ($registro->codigo ?? 0),
                    'sistema' => (string) ($registro->sistema ?? ''),
                    'referencia' => (string) ($registro->referencia ?? ''),
                    'status' => (string) ($registro->status ?? ''),
                    'payload' => json_decode((string) ($registro->payload ?? '[]'), true) ?: [],
                    'protocolo_externo' => $registro->protocolo_externo,
                    'mensagem' => $registro->mensagem,
                    'tentativas_reprocessamento' => (int) ($registro->tentativas_reprocessamento ?? 0),
                    'atualizado_em' => $registro->atualizado_em,
                ];
            })->all();
        };

        try {
            return $consulta('integracao.integracao_envio_status');
        } catch (QueryException $exception) {
            return $consulta('integracao_envio_status');
        }
    }

    public function incrementarTentativaReprocessamento(int $codigo): void
    {
        $dados = [
            'igs01_tentativas_reprocessamento' => DB::raw('COALESCE(igs01_tentativas_reprocessamento, 0) + 1'),
            'igs01_ultima_tentativa_em' => now(),
            'igs01_atualizado_em' => now(),
        ];

        $this->runUpdateWithFallback($codigo, $dados);
    }

    /**
     * @param array<string, mixed> $dados
     */
    private function runUpdateWithFallback(int $codigo, array $dados): void
    {
        try {
            DB::table('integracao.integracao_envio_status')
                ->where('igs01_codigo', $codigo)
                ->update($dados);
        } catch (QueryException $exception) {
            DB::table('integracao_envio_status')
                ->where('igs01_codigo', $codigo)
                ->update($dados);
        }
    }
}
