<?php

namespace App\Services\Financeiro\Integracao;

use Throwable;

class ChecklistHomologacaoExternaService
{
    private IntegracaoGovernamentalStatusService $statusService;
    private HomologacaoAnexosService $anexosService;

    public function __construct(
        ?IntegracaoGovernamentalStatusService $statusService = null,
        ?HomologacaoAnexosService $anexosService = null
    ) {
        $this->statusService = $statusService ?? new IntegracaoGovernamentalStatusService();
        $this->anexosService = $anexosService ?? new HomologacaoAnexosService();
    }

    /**
     * @param array<int, string>|null $sistemas
     * @return array<string, mixed>
     */
    public function gerarResumo(?array $sistemas = null, ?string $diretorioAnexos = null, int $limite = 200): array
    {
        $sistemas = $sistemas ?? ['SICONFI', 'TCE_PR', 'PORTAL_TRANSPARENCIA'];
        $diretorioAnexos = $diretorioAnexos ?: 'docs/anexos_homologacao_assinados';

        $resultadoSistemas = [];
        $totais = [
            'apto' => 0,
            'em_homologacao' => 0,
            'bloqueado' => 0,
        ];

        foreach ($sistemas as $sistema) {
            try {
                $resumo = $this->statusService->gerarResumoHomologacao($sistema, $limite);
            } catch (Throwable $e) {
                $resumo = [
                    'totais' => [
                        'pendente' => 1,
                        'enviado' => 0,
                        'aceito' => 0,
                        'rejeitado' => 0,
                    ],
                    'erro_coleta' => $e->getMessage(),
                ];
            }

            $sistemaResultado = $this->avaliarSistema($sistema, $resumo);
            $totais[$sistemaResultado['status']]++;
            $resultadoSistemas[] = $sistemaResultado;
        }

        $anexos = $this->anexosService->validarDiretorio($diretorioAnexos);

        $statusFinal = 'plano_de_acao_obrigatorio';
        if ($totais['bloqueado'] === 0 && $totais['em_homologacao'] === 0 && ($anexos['status'] ?? 'pendente') === 'ok') {
            $statusFinal = 'apto_para_banca';
        } elseif ($totais['bloqueado'] === 0) {
            $statusFinal = 'apto_com_ressalvas';
        }

        return [
            'sistemas' => $resultadoSistemas,
            'totais' => $totais,
            'anexos' => $anexos,
            'status_final' => $statusFinal,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 13 - Checklist de Homologacao Externa',
            '',
            '- Status final: ' . (string) ($resumo['status_final'] ?? 'plano_de_acao_obrigatorio'),
            '- Sistemas aptos: ' . (int) (($resumo['totais']['apto'] ?? 0)),
            '- Sistemas em homologacao: ' . (int) (($resumo['totais']['em_homologacao'] ?? 0)),
            '- Sistemas bloqueados: ' . (int) (($resumo['totais']['bloqueado'] ?? 0)),
            '',
            '## Criterios de aceite por sistema',
            '- C1: deve possuir ao menos 1 registro `aceito`.',
            '- C2: nao pode possuir registro `rejeitado`.',
            '- C3: nao pode possuir registro `pendente`.',
            '',
            '## Resultado por sistema',
        ];

        foreach (($resumo['sistemas'] ?? []) as $sistema) {
            $criterios = [];
            foreach (($sistema['criterios'] ?? []) as $chave => $valor) {
                $criterios[] = (string) $chave . '=' . (string) $valor;
            }

            $linhas[] = '- [' . strtoupper((string) ($sistema['status'] ?? 'em_homologacao')) . '] '
                . (string) ($sistema['sistema'] ?? '-')
                . ' | pendente=' . (int) (($sistema['totais']['pendente'] ?? 0))
                . ', enviado=' . (int) (($sistema['totais']['enviado'] ?? 0))
                . ', aceito=' . (int) (($sistema['totais']['aceito'] ?? 0))
                . ', rejeitado=' . (int) (($sistema['totais']['rejeitado'] ?? 0))
                . ' | criterios=' . implode(',', $criterios);

            if ((string) ($sistema['erro_coleta'] ?? '') !== '') {
                $linhas[] = '  - Aviso: coleta de status indisponivel no ambiente atual.';
            }
        }

        $anexos = $resumo['anexos'] ?? [];
        $linhas[] = '';
        $linhas[] = '## Anexos assinados';
        $linhas[] = '- Diretorio: `' . (string) ($anexos['diretorio'] ?? '') . '`';
        $linhas[] = '- Status: ' . (string) ($anexos['status'] ?? 'pendente');
        $linhas[] = '- Ausentes: ' . (count($anexos['ausentes'] ?? []) > 0 ? implode(', ', $anexos['ausentes']) : 'nenhum');
        $linhas[] = '- Vazios: ' . (count($anexos['vazios'] ?? []) > 0 ? implode(', ', $anexos['vazios']) : 'nenhum');

        return implode("\n", $linhas) . "\n";
    }

    /**
     * @param array<string, mixed> $resumoSistema
     * @return array<string, mixed>
     */
    private function avaliarSistema(string $sistema, array $resumoSistema): array
    {
        $totais = [
            'pendente' => (int) (($resumoSistema['totais']['pendente'] ?? 0)),
            'enviado' => (int) (($resumoSistema['totais']['enviado'] ?? 0)),
            'aceito' => (int) (($resumoSistema['totais']['aceito'] ?? 0)),
            'rejeitado' => (int) (($resumoSistema['totais']['rejeitado'] ?? 0)),
        ];

        $criterio1 = $totais['aceito'] > 0;
        $criterio2 = $totais['rejeitado'] === 0;
        $criterio3 = $totais['pendente'] === 0;

        if ($criterio1 && $criterio2 && $criterio3) {
            $status = 'apto';
        } elseif (!$criterio2) {
            $status = 'bloqueado';
        } else {
            $status = 'em_homologacao';
        }

        return [
            'sistema' => $sistema,
            'totais' => $totais,
            'criterios' => [
                'C1' => $criterio1 ? 'ok' : 'falha',
                'C2' => $criterio2 ? 'ok' : 'falha',
                'C3' => $criterio3 ? 'ok' : 'falha',
            ],
            'status' => $status,
            'erro_coleta' => (string) ($resumoSistema['erro_coleta'] ?? ''),
        ];
    }
}
