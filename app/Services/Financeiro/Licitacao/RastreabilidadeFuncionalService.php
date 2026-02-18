<?php

namespace App\Services\Financeiro\Licitacao;

class RastreabilidadeFuncionalService
{
    /**
     * @param array<int, array<string, mixed>>|null $cenarios
     * @return array<string, mixed>
     */
    public function gerarResumo(?array $cenarios = null): array
    {
        $cenarios = $cenarios ?? $this->cenariosPadrao();

        $resultado = [];
        $totais = [
            'pronto' => 0,
            'parcial' => 0,
        ];

        foreach ($cenarios as $cenario) {
            $evidencias = $cenario['evidencias'] ?? [];
            $ok = 0;
            $faltantes = [];

            foreach ($evidencias as $evidencia) {
                $arquivo = (string) ($evidencia['arquivo'] ?? '');
                if ($arquivo !== '' && is_file($arquivo)) {
                    $ok++;
                    continue;
                }

                $faltantes[] = [
                    'descricao' => (string) ($evidencia['descricao'] ?? 'evidencia sem descricao'),
                    'arquivo' => $arquivo,
                ];
            }

            $totalEvidencias = count($evidencias);
            $percentual = $totalEvidencias > 0 ? round(($ok / $totalEvidencias) * 100, 2) : 0.0;
            $status = $percentual >= 100.0 ? 'pronto' : 'parcial';
            $totais[$status]++;

            $resultado[] = [
                'id' => (string) ($cenario['id'] ?? ''),
                'modulo' => (string) ($cenario['modulo'] ?? ''),
                'objetivo' => (string) ($cenario['objetivo'] ?? ''),
                'pre_condicoes' => $this->stringArray($cenario['pre_condicoes'] ?? []),
                'passos' => $this->stringArray($cenario['passos'] ?? []),
                'status' => $status,
                'total_evidencias' => $totalEvidencias,
                'evidencias_ok' => $ok,
                'evidencias_faltantes' => count($faltantes),
                'percentual' => $percentual,
                'faltantes' => $faltantes,
            ];
        }

        $totalCenarios = count($resultado);
        $percentualGlobal = $totalCenarios > 0
            ? round(($totais['pronto'] / $totalCenarios) * 100, 2)
            : 0.0;

        $statusRecomendado = $totais['parcial'] === 0 ? 'pronto_para_homologacao' : 'homologacao_assistida';

        return [
            'total_cenarios' => $totalCenarios,
            'totais' => $totais,
            'percentual_global' => $percentualGlobal,
            'status_recomendado' => $statusRecomendado,
            'cenarios' => $resultado,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 12 - Rastreabilidade Funcional por Cenario',
            '',
            '- Total de cenarios: ' . (int) ($resumo['total_cenarios'] ?? 0),
            '- Prontos: ' . (int) (($resumo['totais']['pronto'] ?? 0)),
            '- Parciais: ' . (int) (($resumo['totais']['parcial'] ?? 0)),
            '- Percentual global: ' . number_format((float) ($resumo['percentual_global'] ?? 0), 2, '.', '') . '%',
            '- Status recomendado: ' . (string) ($resumo['status_recomendado'] ?? 'homologacao_assistida'),
            '',
        ];

        foreach (($resumo['cenarios'] ?? []) as $cenario) {
            $linhas[] = '## ' . (string) ($cenario['id'] ?? '-') . ' - ' . (string) ($cenario['modulo'] ?? '-');
            $linhas[] = '- Objetivo: ' . (string) ($cenario['objetivo'] ?? '-');
            $linhas[] = '- Status: ' . (string) ($cenario['status'] ?? 'parcial');
            $linhas[] = '- Cobertura de evidencias: '
                . (int) ($cenario['evidencias_ok'] ?? 0) . '/' . (int) ($cenario['total_evidencias'] ?? 0)
                . ' (' . number_format((float) ($cenario['percentual'] ?? 0), 2, '.', '') . '%)';
            $linhas[] = '- Pre-condicoes:';
            foreach (($cenario['pre_condicoes'] ?? []) as $item) {
                $linhas[] = '  - ' . $item;
            }
            $linhas[] = '- Passos:';
            foreach (($cenario['passos'] ?? []) as $passo) {
                $linhas[] = '  - ' . $passo;
            }
            if (($cenario['evidencias_faltantes'] ?? 0) > 0) {
                $linhas[] = '- Evidencias faltantes:';
                foreach (($cenario['faltantes'] ?? []) as $faltante) {
                    $linhas[] = '  - ' . (string) ($faltante['descricao'] ?? '-') . ' (`' . (string) ($faltante['arquivo'] ?? '') . '`)';
                }
            } else {
                $linhas[] = '- Evidencias faltantes: nenhuma.';
            }
            $linhas[] = '';
        }

        return implode("\n", $linhas) . "\n";
    }

    /**
     * @param mixed $valor
     * @return array<int, string>
     */
    private function stringArray($valor): array
    {
        if (!is_array($valor)) {
            return [];
        }

        $saida = [];
        foreach ($valor as $item) {
            if (is_string($item) && trim($item) !== '') {
                $saida[] = $item;
            }
        }

        return $saida;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cenariosPadrao(): array
    {
        return [
            [
                'id' => 'C1',
                'modulo' => 'M1 Contabilidade Publica',
                'objetivo' => 'Gerar balancos e demonstracoes obrigatorias com rastreabilidade.',
                'pre_condicoes' => [
                    'Ambiente de homologacao com base contabil carregada.',
                    'Permissao para execucao de relatorios financeiros.',
                ],
                'passos' => [
                    'Executar comando de geracao de balancos.',
                    'Executar comando de demonstracoes contabeis.',
                    'Anexar saidas e logs ao dossie da licitacao.',
                ],
                'evidencias' => [
                    ['descricao' => 'Comando de balancos', 'arquivo' => 'app/Console/Commands/Financeiro/GerarBalancosCommand.php'],
                    ['descricao' => 'Comando de demonstracoes', 'arquivo' => 'app/Console/Commands/Financeiro/GerarDemonstracoesContabeisCommand.php'],
                    ['descricao' => 'Matriz do modulo 1', 'arquivo' => 'docs/matriz_modulo1_contabilidade_publica_licitacao.md'],
                ],
            ],
            [
                'id' => 'C2',
                'modulo' => 'M2 Orcamento e Planejamento',
                'objetivo' => 'Validar ciclo PPA/LDO/LOA com criterio de consistencia.',
                'pre_condicoes' => [
                    'Regras de classificacao orcamentaria definidas.',
                    'Perfis tecnicos autorizados para planejamento.',
                ],
                'passos' => [
                    'Conferir requisitos baseline do modulo.',
                    'Executar simulacao com reflexo na execucao.',
                    'Registrar evidencias no dossie tecnico.',
                ],
                'evidencias' => [
                    ['descricao' => 'Requisitos modulo 2', 'arquivo' => 'docs/requisitos_modulo2_orcamento_planejamento.md'],
                    ['descricao' => 'Evidencias TR2', 'arquivo' => 'docs/sprint9_evidencias_tr2_orcamentario.md'],
                    ['descricao' => 'Servico de ciclo da despesa', 'arquivo' => 'app/Services/Financeiro/ExecucaoOrcamentaria/CicloDespesaService.php'],
                ],
            ],
            [
                'id' => 'C3',
                'modulo' => 'M3 Execucao Orcamentaria',
                'objetivo' => 'Comprovar ciclo fixacao->empenho->liquidacao->pagamento.',
                'pre_condicoes' => [
                    'Dotacao inicial disponivel.',
                    'Credor habilitado e regras de bloqueio ativas.',
                ],
                'passos' => [
                    'Executar validacao do ciclo da despesa.',
                    'Registrar bloqueios e alertas aplicados.',
                    'Anexar resultado da execucao ao pacote de evidencias.',
                ],
                'evidencias' => [
                    ['descricao' => 'Comando de ciclo da despesa', 'arquivo' => 'app/Console/Commands/Financeiro/ValidarCicloDespesaCommand.php'],
                    ['descricao' => 'Requisitos modulo 3', 'arquivo' => 'docs/requisitos_modulo3_execucao_orcamentaria.md'],
                    ['descricao' => 'Checklist sprint 8', 'arquivo' => 'docs/sprint8_checklist_execucao.md'],
                ],
            ],
            [
                'id' => 'C4',
                'modulo' => 'M4 Tesouraria e Fluxo de Caixa',
                'objetivo' => 'Comprovar conciliacao, projecao de caixa e alertas operacionais.',
                'pre_condicoes' => [
                    'Contas bancarias e movimentos importados.',
                    'Parametros de janela de previsao configurados.',
                ],
                'passos' => [
                    'Executar conciliacao bancaria.',
                    'Gerar previsao de fluxo de caixa.',
                    'Publicar dashboard de tesouraria para evidencias.',
                ],
                'evidencias' => [
                    ['descricao' => 'Comando de conciliacao bancaria', 'arquivo' => 'app/Console/Commands/Financeiro/ConciliarContaBancariaCommand.php'],
                    ['descricao' => 'Comando de previsao de caixa', 'arquivo' => 'app/Console/Commands/Financeiro/PreverFluxoCaixaCommand.php'],
                    ['descricao' => 'Requisitos modulo 4', 'arquivo' => 'docs/requisitos_modulo4_tesouraria_fluxo_caixa.md'],
                ],
            ],
            [
                'id' => 'C5',
                'modulo' => 'M5 Controle de Despesas e Receitas',
                'objetivo' => 'Comprovar validacao de credor, retencoes e classificacao de receitas.',
                'pre_condicoes' => [
                    'Cadastro de credor com documentos obrigatorios.',
                    'Regras tributarias parametrizadas.',
                ],
                'passos' => [
                    'Executar validacao de credor e fluxo de despesa.',
                    'Calcular retencoes tributarias.',
                    'Executar classificacao e consolidacao de receitas.',
                ],
                'evidencias' => [
                    ['descricao' => 'Comando de validacao de credor', 'arquivo' => 'app/Console/Commands/Financeiro/ValidarCredorCommand.php'],
                    ['descricao' => 'Comando de retencoes tributarias', 'arquivo' => 'app/Console/Commands/Financeiro/CalcularRetencoesTributariasCommand.php'],
                    ['descricao' => 'Requisitos modulo 5', 'arquivo' => 'docs/requisitos_modulo5_controle_despesas_receitas.md'],
                ],
            ],
        ];
    }
}
