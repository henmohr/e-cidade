<?php

namespace App\Services\Financeiro\Licitacao;

class CoberturaModulosFinanceirosService
{
    /**
     * @return array<string, mixed>
     */
    public function gerarResumo(?array $modulos = null): array
    {
        $modulos = $modulos ?? $this->definicaoPadrao();

        $resultadoModulos = [];
        $totais = [
            'atingido' => 0,
            'parcial' => 0,
            'pendente' => 0,
        ];

        foreach ($modulos as $modulo) {
            $checks = $modulo['checks'] ?? [];
            $ok = 0;
            $faltantes = [];

            foreach ($checks as $check) {
                $passou = $this->avaliarCheck($check);
                if ($passou) {
                    $ok++;
                    continue;
                }

                $faltantes[] = [
                    'descricao' => (string) ($check['descricao'] ?? 'Check sem descricao'),
                    'tipo' => (string) ($check['tipo'] ?? 'arquivo'),
                    'valor' => (string) (($check['arquivo'] ?? $check['comando'] ?? '')),
                ];
            }

            $totalChecks = count($checks);
            $percentual = $totalChecks > 0 ? round(($ok / $totalChecks) * 100, 2) : 0.0;
            $status = $this->classificarStatus($percentual);
            $totais[$status]++;

            $resultadoModulos[] = [
                'id' => (string) ($modulo['id'] ?? ''),
                'nome' => (string) ($modulo['nome'] ?? ''),
                'total_checks' => $totalChecks,
                'checks_ok' => $ok,
                'checks_faltantes' => count($faltantes),
                'percentual' => $percentual,
                'status' => $status,
                'faltantes' => $faltantes,
            ];
        }

        $totalModulos = count($resultadoModulos);
        $percentualGlobal = $totalModulos > 0
            ? round((($totais['atingido'] + ($totais['parcial'] * 0.5)) / $totalModulos) * 100, 2)
            : 0.0;

        if ($totais['pendente'] === 0 && $totais['parcial'] === 0) {
            $statusRecomendado = 'apto_para_banca';
        } elseif ($totais['pendente'] === 0) {
            $statusRecomendado = 'apto_com_ressalvas';
        } else {
            $statusRecomendado = 'plano_de_acao_obrigatorio';
        }

        return [
            'total_modulos' => $totalModulos,
            'totais' => $totais,
            'percentual_global' => $percentualGlobal,
            'status_recomendado' => $statusRecomendado,
            'modulos' => $resultadoModulos,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 11 - Cobertura dos Modulos Financeiros',
            '',
            '- Total de modulos avaliados: ' . (int) ($resumo['total_modulos'] ?? 0),
            '- Atingidos: ' . (int) (($resumo['totais']['atingido'] ?? 0)),
            '- Parciais: ' . (int) (($resumo['totais']['parcial'] ?? 0)),
            '- Pendentes: ' . (int) (($resumo['totais']['pendente'] ?? 0)),
            '- Percentual global: ' . number_format((float) ($resumo['percentual_global'] ?? 0), 2, '.', '') . '%',
            '- Status recomendado: ' . (string) ($resumo['status_recomendado'] ?? 'plano_de_acao_obrigatorio'),
            '',
            '## Resultado por modulo',
        ];

        foreach (($resumo['modulos'] ?? []) as $modulo) {
            $linhas[] = '- [' . strtoupper((string) ($modulo['status'] ?? 'pendente')) . '] '
                . (string) ($modulo['id'] ?? '-') . ' - '
                . (string) ($modulo['nome'] ?? '-')
                . ' | checks: ' . (int) ($modulo['checks_ok'] ?? 0) . '/' . (int) ($modulo['total_checks'] ?? 0)
                . ' | cobertura: ' . number_format((float) ($modulo['percentual'] ?? 0), 2, '.', '') . '%';

            foreach (($modulo['faltantes'] ?? []) as $faltante) {
                $linhas[] = '  - Faltante: ' . (string) ($faltante['descricao'] ?? 'sem descricao');
            }
        }

        return implode("\n", $linhas) . "\n";
    }

    private function classificarStatus(float $percentual): string
    {
        if ($percentual >= 100.0) {
            return 'atingido';
        }

        if ($percentual >= 70.0) {
            return 'parcial';
        }

        return 'pendente';
    }

    /**
     * @param array<string, mixed> $check
     */
    private function avaliarCheck(array $check): bool
    {
        $tipo = (string) ($check['tipo'] ?? 'arquivo');
        if ($tipo === 'arquivo') {
            return is_file((string) ($check['arquivo'] ?? ''));
        }

        if ($tipo === 'um_de') {
            $arquivos = $check['arquivos'] ?? [];
            if (!is_array($arquivos)) {
                return false;
            }

            foreach ($arquivos as $arquivo) {
                if (is_string($arquivo) && is_file($arquivo)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function definicaoPadrao(): array
    {
        return [
            [
                'id' => 'M1',
                'nome' => 'Contabilidade Publica',
                'checks' => [
                    ['descricao' => 'Servico de balancos', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Relatorio/BalancoService.php'],
                    ['descricao' => 'Servico de demonstracoes contabeis', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Relatorio/DemonstracaoContabilService.php'],
                    ['descricao' => 'Comando de geracao de balancos', 'tipo' => 'arquivo', 'arquivo' => 'app/Console/Commands/Financeiro/GerarBalancosCommand.php'],
                    ['descricao' => 'Matriz de aderencia do modulo 1', 'tipo' => 'arquivo', 'arquivo' => 'docs/matriz_modulo1_contabilidade_publica_licitacao.md'],
                ],
            ],
            [
                'id' => 'M2',
                'nome' => 'Orcamento e Planejamento (PPA, LDO e LOA)',
                'checks' => [
                    ['descricao' => 'Evidencia tecnica do item TR 2', 'tipo' => 'arquivo', 'arquivo' => 'docs/sprint9_evidencias_tr2_orcamentario.md'],
                    ['descricao' => 'Requisitos do modulo 2', 'tipo' => 'arquivo', 'arquivo' => 'docs/requisitos_modulo2_orcamento_planejamento.md'],
                    ['descricao' => 'Base de ciclo orcamentario para execucao', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/ExecucaoOrcamentaria/CicloDespesaService.php'],
                ],
            ],
            [
                'id' => 'M3',
                'nome' => 'Execucao Orcamentaria',
                'checks' => [
                    ['descricao' => 'Servico do ciclo da despesa publica', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/ExecucaoOrcamentaria/CicloDespesaService.php'],
                    ['descricao' => 'Comando de validacao do ciclo da despesa', 'tipo' => 'arquivo', 'arquivo' => 'app/Console/Commands/Financeiro/ValidarCicloDespesaCommand.php'],
                    ['descricao' => 'Requisitos do modulo 3', 'tipo' => 'arquivo', 'arquivo' => 'docs/requisitos_modulo3_execucao_orcamentaria.md'],
                ],
            ],
            [
                'id' => 'M4',
                'nome' => 'Tesouraria e Fluxo de Caixa',
                'checks' => [
                    ['descricao' => 'Servico de conciliacao bancaria', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Tesouraria/ConciliacaoBancariaService.php'],
                    ['descricao' => 'Servico de previsao de fluxo de caixa', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Tesouraria/FluxoCaixaService.php'],
                    ['descricao' => 'Comando de dashboard de tesouraria', 'tipo' => 'arquivo', 'arquivo' => 'app/Console/Commands/Financeiro/DashboardTesourariaCommand.php'],
                    ['descricao' => 'Requisitos do modulo 4', 'tipo' => 'arquivo', 'arquivo' => 'docs/requisitos_modulo4_tesouraria_fluxo_caixa.md'],
                ],
            ],
            [
                'id' => 'M5',
                'nome' => 'Controle de Despesas e Receitas',
                'checks' => [
                    ['descricao' => 'Validacao de credor', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Credor/ValidacaoCredorService.php'],
                    ['descricao' => 'Calculo de retencoes tributarias', 'tipo' => 'arquivo', 'arquivo' => 'app/Services/Financeiro/Retencoes/CalculadoraRetencoesTributariasService.php'],
                    ['descricao' => 'Controle e classificacao de receitas', 'tipo' => 'um_de', 'arquivos' => [
                        'app/Services/Financeiro/Receita/ControleReceitasService.php',
                        'app/Services/Financeiro/Receita/ClassificacaoReceitaService.php',
                    ]],
                    ['descricao' => 'Requisitos do modulo 5', 'tipo' => 'arquivo', 'arquivo' => 'docs/requisitos_modulo5_controle_despesas_receitas.md'],
                ],
            ],
        ];
    }
}
