<?php

namespace App\Services\Financeiro\Licitacao;

class RelatorioExecutivoLicitacaoService
{
    /**
     * @param array<string, string>|null $arquivos
     * @return array<string, mixed>
     */
    public function gerarResumo(?array $arquivos = null): array
    {
        $arquivos = $arquivos ?? $this->arquivosPadrao();

        $s11 = $this->carregarJson($arquivos['sprint11'] ?? '');
        $s12 = $this->carregarJson($arquivos['sprint12'] ?? '');
        $s14 = $this->carregarJson($arquivos['sprint14'] ?? '');
        $s15 = $this->carregarJson($arquivos['sprint15'] ?? '');

        $pendencias = [];
        if ($s11 === null) {
            $pendencias[] = 'Ausencia de cobertura dos modulos (Sprint 11).';
        }
        if ($s12 === null) {
            $pendencias[] = 'Ausencia de rastreabilidade funcional (Sprint 12).';
        }
        if ($s14 === null) {
            $pendencias[] = 'Ausencia de checklist de homologacao externa (Sprint 14).';
        }
        if ($s15 === null) {
            $pendencias[] = 'Ausencia do gate final consolidado (Sprint 15).';
        }

        $statusEntrega = (string) (($s15['status_final'] ?? 'bloqueado'));
        $decisao = $statusEntrega === 'apto_para_entrega' && count($pendencias) === 0
            ? 'recomendado_para_protocolo'
            : 'segurar_envio';

        $recomendacoes = $this->gerarRecomendacoes($s11, $s12, $s14, $s15);

        return [
            'gerado_em' => date('c'),
            'decisao' => $decisao,
            'status_entrega' => $statusEntrega,
            'indicadores' => [
                'cobertura_modulos_percentual' => (float) (($s11['percentual_global'] ?? 0)),
                'rastreabilidade_status' => (string) (($s12['status_recomendado'] ?? 'indisponivel')),
                'homologacao_status' => (string) (($s14['status_final'] ?? 'indisponivel')),
                'gate_status' => $statusEntrega,
            ],
            'pendencias' => $pendencias,
            'recomendacoes' => $recomendacoes,
            'arquivos_base' => $arquivos,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 16 - Relatorio Executivo de Prontidao',
            '',
            '- Gerado em: ' . (string) ($resumo['gerado_em'] ?? ''),
            '- Decisao executiva: ' . (string) ($resumo['decisao'] ?? 'segurar_envio'),
            '- Status de entrega: ' . (string) ($resumo['status_entrega'] ?? 'bloqueado'),
            '',
            '## Indicadores consolidados',
            '- Cobertura de modulos: ' . number_format((float) (($resumo['indicadores']['cobertura_modulos_percentual'] ?? 0)), 2, '.', '') . '%',
            '- Rastreabilidade funcional: ' . (string) (($resumo['indicadores']['rastreabilidade_status'] ?? 'indisponivel')),
            '- Homologacao externa: ' . (string) (($resumo['indicadores']['homologacao_status'] ?? 'indisponivel')),
            '- Gate final: ' . (string) (($resumo['indicadores']['gate_status'] ?? 'bloqueado')),
            '',
            '## Pendencias',
        ];

        if (count($resumo['pendencias'] ?? []) === 0) {
            $linhas[] = '- Nenhuma pendencia executiva identificada.';
        } else {
            foreach (($resumo['pendencias'] ?? []) as $pendencia) {
                $linhas[] = '- ' . (string) $pendencia;
            }
        }

        $linhas[] = '';
        $linhas[] = '## Recomendacoes imediatas';
        foreach (($resumo['recomendacoes'] ?? []) as $recomendacao) {
            $linhas[] = '- ' . (string) $recomendacao;
        }

        return implode("\n", $linhas) . "\n";
    }

    /**
     * @return array<string, string>
     */
    public function arquivosPadrao(): array
    {
        return [
            'sprint11' => 'docs/sprint11_cobertura_modulos/cobertura_modulos_financeiros.json',
            'sprint12' => 'docs/sprint12_rastreabilidade_funcional/rastreabilidade_funcional.json',
            'sprint14' => 'docs/sprint14_homologacao_externa/checklist_homologacao_externa.json',
            'sprint15' => 'docs/sprint15_gate_entrega/gate_entrega_licitacao.json',
        ];
    }

    /**
     * @param array<string, mixed>|null $s11
     * @param array<string, mixed>|null $s12
     * @param array<string, mixed>|null $s14
     * @param array<string, mixed>|null $s15
     * @return array<int, string>
     */
    private function gerarRecomendacoes(?array $s11, ?array $s12, ?array $s14, ?array $s15): array
    {
        $recomendacoes = [];

        if ((float) (($s11['percentual_global'] ?? 0)) < 100.0) {
            $recomendacoes[] = 'Fechar os checks restantes de cobertura dos modulos M1-M5.';
        }
        if ((string) (($s12['status_recomendado'] ?? '')) !== 'pronto_para_homologacao') {
            $recomendacoes[] = 'Concluir cenarios de rastreabilidade funcional pendentes.';
        }
        if ((string) (($s14['status_final'] ?? '')) !== 'apto_para_banca') {
            $recomendacoes[] = 'Atualizar protocolos oficiais da homologacao externa e regenerar checklist.';
        }
        if ((string) (($s15['status_final'] ?? '')) !== 'apto_para_entrega') {
            $recomendacoes[] = 'Resolver pendencias do gate final antes de protocolar proposta.';
        }

        if (count($recomendacoes) === 0) {
            $recomendacoes[] = 'Protocolar pacote tecnico e manter monitoramento de regressao semanal.';
        }

        return $recomendacoes;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function carregarJson(string $arquivo): ?array
    {
        if ($arquivo === '' || !is_file($arquivo)) {
            return null;
        }

        $conteudo = (string) file_get_contents($arquivo);
        $dados = json_decode($conteudo, true);

        return is_array($dados) ? $dados : null;
    }
}
