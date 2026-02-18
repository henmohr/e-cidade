<?php

namespace App\Services\Financeiro\Integracao;

use Symfony\Component\Yaml\Yaml;
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
    public function gerarResumo(
        ?array $sistemas = null,
        ?string $diretorioAnexos = null,
        int $limite = 200,
        ?string $arquivoProtocolos = null,
        bool $modoOffline = false
    ): array {
        $sistemas = $sistemas ?? ['SICONFI', 'TCE_PR', 'PORTAL_TRANSPARENCIA'];
        $diretorioAnexos = $diretorioAnexos ?: 'docs/anexos_homologacao_assinados';
        $cargaProtocolos = $this->carregarProtocolos($arquivoProtocolos);
        $protocolos = $cargaProtocolos['sistemas'];
        $alertas = $cargaProtocolos['alertas'];

        $resultadoSistemas = [];
        $totais = [
            'apto' => 0,
            'em_homologacao' => 0,
            'bloqueado' => 0,
        ];

        foreach ($sistemas as $sistema) {
            if (isset($protocolos[$sistema])) {
                $resumo = [
                    'totais' => $protocolos[$sistema],
                    'fonte' => 'arquivo_protocolos',
                ];
            } elseif ($modoOffline) {
                $resumo = [
                    'totais' => [
                        'pendente' => 1,
                        'enviado' => 0,
                        'aceito' => 0,
                        'rejeitado' => 0,
                    ],
                    'fonte' => 'offline_sem_protocolo',
                    'erro_coleta' => 'Modo offline sem totais oficiais para o sistema.',
                ];
            } else {
                try {
                    $resumo = $this->statusService->gerarResumoHomologacao($sistema, $limite);
                    $resumo['fonte'] = 'repositorio';
                } catch (Throwable $e) {
                    $resumo = [
                        'totais' => [
                            'pendente' => 1,
                            'enviado' => 0,
                            'aceito' => 0,
                            'rejeitado' => 0,
                        ],
                        'fonte' => 'repositorio_indisponivel',
                        'erro_coleta' => $e->getMessage(),
                    ];
                }
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
            'metadados' => [
                'modo_offline' => $modoOffline,
                'arquivo_protocolos' => $arquivoProtocolos,
                'alertas' => $alertas,
            ],
            'status_final' => $statusFinal,
        ];
    }

    /**
     * @param array<string, mixed> $resumo
     */
    public function gerarMarkdown(array $resumo): string
    {
        $linhas = [
            '# Sprint 14 - Checklist de Homologacao Externa',
            '',
            '- Status final: ' . (string) ($resumo['status_final'] ?? 'plano_de_acao_obrigatorio'),
            '- Sistemas aptos: ' . (int) (($resumo['totais']['apto'] ?? 0)),
            '- Sistemas em homologacao: ' . (int) (($resumo['totais']['em_homologacao'] ?? 0)),
            '- Sistemas bloqueados: ' . (int) (($resumo['totais']['bloqueado'] ?? 0)),
            '- Modo offline: ' . (((bool) ($resumo['metadados']['modo_offline'] ?? false)) ? 'sim' : 'nao'),
            '- Arquivo de protocolos: ' . (string) (($resumo['metadados']['arquivo_protocolos'] ?? '') ?: 'nao informado'),
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
                . ' | criterios=' . implode(',', $criterios)
                . ' | fonte=' . (string) ($sistema['fonte'] ?? 'desconhecida');

            if ((string) ($sistema['erro_coleta'] ?? '') !== '') {
                $linhas[] = 'Aviso ' . (string) ($sistema['sistema'] ?? '-')
                    . ': coleta de status indisponivel no ambiente atual.';
            }
        }

        foreach (($resumo['metadados']['alertas'] ?? []) as $alerta) {
            $linhas[] = '- Alerta: ' . (string) $alerta;
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
            'fonte' => (string) ($resumoSistema['fonte'] ?? 'desconhecida'),
            'erro_coleta' => (string) ($resumoSistema['erro_coleta'] ?? ''),
        ];
    }

    /**
     * @return array{ sistemas: array<string, array<string, int>>, alertas: array<int, string> }
     */
    private function carregarProtocolos(?string $arquivoProtocolos): array
    {
        if ($arquivoProtocolos === null || trim($arquivoProtocolos) === '') {
            return ['sistemas' => [], 'alertas' => []];
        }

        $alertas = [];
        if (!is_file($arquivoProtocolos)) {
            $alertas[] = 'Arquivo de protocolos nao encontrado: ' . $arquivoProtocolos;
            return ['sistemas' => [], 'alertas' => $alertas];
        }

        $extensao = strtolower((string) pathinfo($arquivoProtocolos, PATHINFO_EXTENSION));
        $conteudoBruto = [];

        if (in_array($extensao, ['yaml', 'yml'], true)) {
            $conteudoBruto = Yaml::parseFile($arquivoProtocolos);
        } else {
            $raw = (string) file_get_contents($arquivoProtocolos);
            $conteudoBruto = json_decode($raw, true);
        }

        if (!is_array($conteudoBruto)) {
            $alertas[] = 'Formato invalido no arquivo de protocolos: ' . $arquivoProtocolos;
            return ['sistemas' => [], 'alertas' => $alertas];
        }

        $dadosSistemas = $conteudoBruto['sistemas'] ?? $conteudoBruto;
        if (!is_array($dadosSistemas)) {
            $alertas[] = 'Campo "sistemas" invalido no arquivo de protocolos.';
            return ['sistemas' => [], 'alertas' => $alertas];
        }

        $normalizado = [];
        foreach ($dadosSistemas as $sistema => $totais) {
            if (!is_string($sistema) || !is_array($totais)) {
                continue;
            }

            $normalizado[$sistema] = [
                'pendente' => max(0, (int) ($totais['pendente'] ?? 0)),
                'enviado' => max(0, (int) ($totais['enviado'] ?? 0)),
                'aceito' => max(0, (int) ($totais['aceito'] ?? 0)),
                'rejeitado' => max(0, (int) ($totais['rejeitado'] ?? 0)),
            ];
        }

        if (count($normalizado) === 0) {
            $alertas[] = 'Nenhum sistema valido foi carregado do arquivo de protocolos.';
        }

        return ['sistemas' => $normalizado, 'alertas' => $alertas];
    }
}
