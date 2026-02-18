<?php

namespace App\Services\Financeiro\Receita;

use App\Repositories\Financeiro\Receita\ControleReceitasRepository;
use App\Repositories\Financeiro\Receita\ControleReceitasRepositoryInterface;

class ControleReceitasService
{
    private ControleReceitasRepositoryInterface $repository;

    public function __construct(?ControleReceitasRepositoryInterface $repository = null)
    {
        $this->repository = $repository ?? new ControleReceitasRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function consolidar(?string $dataInicial = null, ?string $dataFinal = null): array
    {
        $linhas = $this->repository->obterReceitasConsolidadas($dataInicial, $dataFinal);

        $tributarias = [];
        $transferencias = [];
        $demais = [];

        $totalTributarias = 0.0;
        $totalTransferencias = 0.0;
        $totalDemais = 0.0;

        foreach ($linhas as $linha) {
            $codigo = (string) ($linha['codigo_receita'] ?? '');
            $descricao = strtoupper((string) (($linha['descricao_completa'] ?? '') ?: ($linha['descricao_curta'] ?? '')));
            $valor = (float) ($linha['valor_total'] ?? 0);
            $item = [
                'codigo_receita' => $codigo,
                'descricao' => $descricao,
                'valor_total' => round($valor, 2),
            ];

            if ($this->isTransferenciaIntergovernamental($descricao, $codigo)) {
                $transferencias[] = $item;
                $totalTransferencias += $valor;
                continue;
            }

            if ($this->isReceitaTributaria($descricao, $codigo)) {
                $tributarias[] = $item;
                $totalTributarias += $valor;
                continue;
            }

            $demais[] = $item;
            $totalDemais += $valor;
        }

        return [
            'periodo' => [
                'data_inicial' => $dataInicial,
                'data_final' => $dataFinal,
            ],
            'totais' => [
                'tributarias' => round($totalTributarias, 2),
                'transferencias_intergovernamentais' => round($totalTransferencias, 2),
                'demais_receitas' => round($totalDemais, 2),
            ],
            'tributarias' => $tributarias,
            'transferencias_intergovernamentais' => $transferencias,
            'demais_receitas' => $demais,
        ];
    }

    private function isReceitaTributaria(string $descricao, string $codigo): bool
    {
        $prefixo = substr(preg_replace('/\D+/', '', $codigo) ?? '', 0, 1);
        $palavras = ['IPTU', 'ISS', 'ISSQN', 'ITBI', 'IMPOSTO', 'TAXA', 'CONTRIBUICAO DE MELHORIA'];

        if ($prefixo === '1' && str_contains($descricao, 'TRIBUT')) {
            return true;
        }

        foreach ($palavras as $palavra) {
            if (str_contains($descricao, $palavra)) {
                return true;
            }
        }

        return false;
    }

    private function isTransferenciaIntergovernamental(string $descricao, string $codigo): bool
    {
        $prefixo = substr(preg_replace('/\D+/', '', $codigo) ?? '', 0, 1);
        $palavras = ['TRANSFERENCIA', 'FPM', 'ICMS', 'IPI', 'FUNDEB', 'SUS', 'FNDE'];

        if (in_array($prefixo, ['1', '2'], true) && str_contains($descricao, 'TRANSFER')) {
            return true;
        }

        foreach ($palavras as $palavra) {
            if (str_contains($descricao, $palavra)) {
                return true;
            }
        }

        return false;
    }
}

