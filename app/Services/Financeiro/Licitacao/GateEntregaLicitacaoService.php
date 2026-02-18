<?php

namespace App\Services\Financeiro\Licitacao;

class GateEntregaLicitacaoService
{
    private JsonArquivoLoaderService $jsonLoader;

    public function __construct(?JsonArquivoLoaderService $jsonLoader = null)
    {
        $this->jsonLoader = $jsonLoader ?? new JsonArquivoLoaderService();
    }

    /**
     * @param array<string, string>|null $arquivos
     * @return array<string, mixed>
     */
    public function gerarResumo(?array $arquivos = null): array
    {
        $arquivos = $arquivos ?? $this->arquivosPadrao();
        $carga = $this->jsonLoader->carregarMapa($arquivos);
        $dados = $carga['dados'];
        $pendencias = $carga['erros'];

        $checks = $this->avaliarChecks($dados);
        foreach ($checks as $check) {
            if (($check['status'] ?? 'falha') !== 'ok') {
                $pendencias[] = (string) ($check['mensagem'] ?? 'pendencia nao detalhada');
            }
        }

        $statusFinal = count($pendencias) === 0 ? 'apto_para_entrega' : 'bloqueado';

        return [
            'gerado_em' => date('c'),
            'status_final' => $statusFinal,
            'arquivos' => $arquivos,
            'checks' => $checks,
            'pendencias' => $pendencias,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 15 - Gate Final de Entrega da Licitacao',
            '',
            '- Gerado em: ' . (string) ($resumo['gerado_em'] ?? ''),
            '- Status final: ' . (string) ($resumo['status_final'] ?? 'bloqueado'),
            '',
            '## Resultado dos checks',
        ];

        foreach (($resumo['checks'] ?? []) as $check) {
            $linhas[] = '- [' . strtoupper((string) ($check['status'] ?? 'falha')) . '] '
                . (string) ($check['nome'] ?? '-')
                . ': ' . (string) ($check['mensagem'] ?? '-');
        }

        $linhas[] = '';
        $linhas[] = '## Pendencias';
        if (count($resumo['pendencias'] ?? []) === 0) {
            $linhas[] = '- Nenhuma pendencia bloqueante.';
        } else {
            foreach (($resumo['pendencias'] ?? []) as $pendencia) {
                $linhas[] = '- ' . (string) $pendencia;
            }
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
            'banca' => 'docs/pacote_final_banca/manifesto_final_banca.json',
        ];
    }

    /**
     * @param array<string, mixed> $dados
     * @return array<int, array<string, string>>
     */
    private function avaliarChecks(array $dados): array
    {
        $checks = [];

        $percentualModulos = (float) (($dados['sprint11']['percentual_global'] ?? 0));
        $checks[] = [
            'nome' => 'Cobertura modulos M1-M5',
            'status' => $percentualModulos >= 100.0 ? 'ok' : 'falha',
            'mensagem' => 'Percentual global da Sprint 11: ' . number_format($percentualModulos, 2, '.', '') . '%',
        ];

        $statusRastreabilidade = (string) (($dados['sprint12']['status_recomendado'] ?? 'homologacao_assistida'));
        $checks[] = [
            'nome' => 'Rastreabilidade funcional',
            'status' => $statusRastreabilidade === 'pronto_para_homologacao' ? 'ok' : 'falha',
            'mensagem' => 'Status da Sprint 12: ' . $statusRastreabilidade,
        ];

        $statusHomologacao = (string) (($dados['sprint14']['status_final'] ?? 'plano_de_acao_obrigatorio'));
        $checks[] = [
            'nome' => 'Homologacao externa',
            'status' => $statusHomologacao === 'apto_para_banca' ? 'ok' : 'falha',
            'mensagem' => 'Status da Sprint 14: ' . $statusHomologacao,
        ];

        $statusBanca = (string) (($dados['banca']['status_final'] ?? 'apto_com_ressalvas'));
        $checks[] = [
            'nome' => 'Pacote final de banca',
            'status' => $statusBanca === 'apto_para_banca' ? 'ok' : 'falha',
            'mensagem' => 'Status do manifesto final de banca: ' . $statusBanca,
        ];

        return $checks;
    }
}
