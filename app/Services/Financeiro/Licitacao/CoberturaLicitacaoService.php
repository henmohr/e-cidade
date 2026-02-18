<?php

namespace App\Services\Financeiro\Licitacao;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

class CoberturaLicitacaoService
{
    public const STATUS_ATINGIDO = 'atingido';
    public const STATUS_PARCIAL = 'parcial';
    public const STATUS_PENDENTE = 'pendente';

    /**
     * @return array<string, mixed>
     */
    public function gerarResumo(string $arquivoYaml): array
    {
        if (!is_file($arquivoYaml)) {
            throw new InvalidArgumentException('Arquivo de matriz nao encontrado: ' . $arquivoYaml);
        }

        $conteudo = Yaml::parseFile($arquivoYaml);
        $itens = $conteudo['itens'] ?? [];

        if (!is_array($itens)) {
            throw new InvalidArgumentException('Estrutura invalida da matriz: campo itens ausente ou invalido.');
        }

        $totais = [
            self::STATUS_ATINGIDO => 0,
            self::STATUS_PARCIAL => 0,
            self::STATUS_PENDENTE => 0,
        ];

        $pendencias = [];

        foreach ($itens as $item) {
            $status = strtolower((string) ($item['status'] ?? self::STATUS_PENDENTE));
            if (!isset($totais[$status])) {
                $status = self::STATUS_PENDENTE;
            }

            $totais[$status]++;

            $evidencia = trim((string) ($item['evidencia_principal'] ?? ''));
            if ($status !== self::STATUS_ATINGIDO || $evidencia === '') {
                $pendencias[] = [
                    'item_tr' => (string) ($item['item_tr'] ?? ''),
                    'sistema' => (string) ($item['sistema'] ?? ''),
                    'status' => $status,
                    'evidencia_principal' => $evidencia,
                    'observacoes' => (string) ($item['observacoes'] ?? ''),
                ];
            }
        }

        $totalItens = count($itens);
        $percentualAtingido = $totalItens > 0 ? round(($totais[self::STATUS_ATINGIDO] / $totalItens) * 100, 2) : 0.0;

        return [
            'arquivo' => $arquivoYaml,
            'total_itens' => $totalItens,
            'totais' => $totais,
            'percentual_atingido' => $percentualAtingido,
            'pendencias' => $pendencias,
            'status_recomendado' => count($pendencias) === 0 ? 'apto_para_banca' : 'apto_com_ressalvas',
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Relatorio de Cobertura da Licitacao',
            '',
            '- Arquivo base: `' . (string) ($resumo['arquivo'] ?? '') . '`',
            '- Total de itens: ' . (int) ($resumo['total_itens'] ?? 0),
            '- Atingidos: ' . (int) (($resumo['totais'][self::STATUS_ATINGIDO] ?? 0)),
            '- Parciais: ' . (int) (($resumo['totais'][self::STATUS_PARCIAL] ?? 0)),
            '- Pendentes: ' . (int) (($resumo['totais'][self::STATUS_PENDENTE] ?? 0)),
            '- Percentual atingido: ' . number_format((float) ($resumo['percentual_atingido'] ?? 0), 2, '.', '') . '%',
            '- Status recomendado: ' . (string) ($resumo['status_recomendado'] ?? 'apto_com_ressalvas'),
            '',
            '## Pendencias',
        ];

        foreach (($resumo['pendencias'] ?? []) as $pendencia) {
            $linhas[] = '- [' . strtoupper((string) ($pendencia['status'] ?? 'pendente')) . '] '
                . (string) ($pendencia['item_tr'] ?? '-') . ' - '
                . (string) ($pendencia['sistema'] ?? '-')
                . ' | evidencia: ' . ((string) ($pendencia['evidencia_principal'] ?? '') ?: 'nao informada');
        }

        if (count($resumo['pendencias'] ?? []) === 0) {
            $linhas[] = '- Sem pendencias registradas.';
        }

        return implode("\n", $linhas) . "\n";
    }
}
