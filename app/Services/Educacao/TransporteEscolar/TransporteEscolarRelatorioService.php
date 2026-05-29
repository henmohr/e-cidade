<?php

namespace App\Services\Educacao\TransporteEscolar;

use Illuminate\Support\Facades\Schema;
use Throwable;

class TransporteEscolarRelatorioService
{
    private TransporteEscolarExportService $exportService;

    public function __construct(TransporteEscolarExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(array $filtros = []): array
    {
        $base = $this->exportService->payload();
        $filtros = $this->normalizarFiltros($filtros);

        $linhasBase = isset($base['linhas']) && is_array($base['linhas']) ? $base['linhas'] : [];
        $veiculos = isset($base['veiculos']) && is_array($base['veiculos']) ? $base['veiculos'] : [];
        $alunosBase = isset($base['alunos']) && is_array($base['alunos']) ? $base['alunos'] : [];

        $alunos = $this->enriquecerAlunos($this->filtrarAlunos($alunosBase, $filtros), $linhasBase);
        $linhas = $this->filtrarLinhas($linhasBase, $alunos, $filtros);
        $periodosDisponiveis = $this->valoresDisponiveis($alunosBase, 'periodo_uso');
        $escolasDisponiveis = $this->valoresDisponiveis($alunosBase, 'escola');

        $statusVeiculos = $this->contagemPorChave($veiculos, 'status');
        $statusLinhas = $this->contagemPorChave($linhas, 'tipo');
        $filtrosAplicados = $this->formatarFiltros($filtros);
        $filtroDescricao = $filtrosAplicados !== [] ? implode(' / ', array_map(static function (array $item): string {
            return $item['label'] . ': ' . $item['valor'];
        }, $filtrosAplicados)) : 'Sem filtros aplicados';

        return [
            'titulo' => 'Relatorios legais do Transporte Escolar',
            'subtitulo' => 'Atendimento aos relatorios obrigatorios e controle da area',
            'status' => 'em_implantacao',
            'gerado_em' => date('d/m/Y H:i'),
            'filtros' => $filtros,
            'filtros_aplicados' => $filtrosAplicados,
            'filtro_descricao' => $filtroDescricao,
            'linhas_disponiveis' => $this->linhasDisponiveis($linhasBase),
            'linha_selecionada' => $this->linhaSelecionada($linhasBase, $filtros['linha']),
            'periodos_disponiveis' => $periodosDisponiveis,
            'escolas_disponiveis' => $escolasDisponiveis,
            'resumo' => [
                [
                    'titulo' => 'Linhas no recorte',
                    'valor' => (string) count($linhas),
                    'detalhe' => 'Roteiros consolidados conforme os filtros aplicados',
                ],
                [
                    'titulo' => 'Veiculos monitorados',
                    'valor' => (string) count($veiculos),
                    'detalhe' => 'Frota propria e terceirizada sob analise',
                ],
                [
                    'titulo' => 'Alunos no recorte',
                    'valor' => (string) count($alunos),
                    'detalhe' => 'Base consolidada para carteira, lista de chamada e validacao',
                ],
                [
                    'titulo' => 'Pendencias documentais',
                    'valor' => '2',
                    'detalhe' => 'Integracao documental e layout oficial em fechamento',
                ],
            ],
            'checklist_legal' => [
                [
                    'codigo' => 'A7.1',
                    'titulo' => 'Roteiros e horarios',
                    'status' => 'disponivel',
                    'evidencia' => 'Listagem consolidada de linhas com horarios, custos e rotas.',
                ],
                [
                    'codigo' => 'A7.2',
                    'titulo' => 'Lista de passageiros',
                    'status' => 'disponivel',
                    'evidencia' => 'Base de alunos vinculados com escola, embarque e linha.',
                ],
                [
                    'codigo' => 'A7.3',
                    'titulo' => 'Controle de frota',
                    'status' => 'disponivel',
                    'evidencia' => 'Veiculos cadastrados por situacao e motorista para auditoria da area.',
                ],
                [
                    'codigo' => 'A7.4',
                    'titulo' => 'Carteira do estudante',
                    'status' => 'disponivel',
                    'evidencia' => 'Impressao em tela e PDF com foto e QR code.',
                ],
                [
                    'codigo' => 'A7.5',
                    'titulo' => 'Relatorio consolidado legal',
                    'status' => 'em_implantacao',
                    'evidencia' => 'Escopo consolidado; fechamento documental ainda depende do layout oficial.',
                ],
                [
                    'codigo' => 'A7.6',
                    'titulo' => 'Arquivo de controle da area',
                    'status' => 'em_implantacao',
                    'evidencia' => 'Saida CSV/PDF publicada como base para conferencia interna.',
                ],
            ],
            'relatorios_obrigatorios' => [
                [
                    'nome' => 'Roteiro por linha',
                    'finalidade' => 'Controle do trajeto e dos horarios operacionais.',
                    'status' => 'disponivel',
                ],
                [
                    'nome' => 'Passageiros por viagem',
                    'finalidade' => 'Conferencia dos alunos transportados por rota.',
                    'status' => 'disponivel',
                ],
                [
                    'nome' => 'Alunos por escola',
                    'finalidade' => 'Apoio ao acompanhamento da rede e da demanda.',
                    'status' => 'disponivel',
                ],
                [
                    'nome' => 'Frota e manutencao',
                    'finalidade' => 'Controle da situacao dos veiculos vinculados.',
                    'status' => 'disponivel',
                ],
                [
                    'nome' => 'Custo por linha',
                    'finalidade' => 'Apuracao de custo e subsidio ao planejamento.',
                    'status' => 'disponivel',
                ],
                [
                    'nome' => 'Relatorio legal consolidado',
                    'finalidade' => 'Pacote de evidencias para atendimento da area.',
                    'status' => 'em_implantacao',
                ],
            ],
            'linhas' => $linhas,
            'veiculos' => $veiculos,
            'alunos' => $alunos,
            'status_veiculos' => $statusVeiculos,
            'status_linhas' => $statusLinhas,
            'integracoes' => [
                'Cadastro escolar e matricula do aluno.',
                'Frota municipal e movimentacao de veiculos.',
                'Documentos e evidencias da secretaria.',
                'Padrao de exportacao SETE em fechamento.',
            ],
            'pendencias' => [
                'Fechar layout documental oficial dos relatorios obrigatorios.',
                'Amarrar exportacao legal com integracao externa SETE.',
            ],
            'base_legal' => $this->baseLegal(),
        ];
    }

    public function csv(array $filtros = []): string
    {
        $payload = $this->payload($filtros);
        $out = fopen('php://temp', 'w+');
        if ($out === false) {
            return '';
        }

        fputcsv($out, ['secao', 'codigo', 'titulo', 'status', 'descricao'], ';');

        foreach ($payload['filtros_aplicados'] as $item) {
            fputcsv($out, [
                'filtro',
                '',
                $item['label'],
                'aplicado',
                $item['valor'],
            ], ';');
        }

        foreach ($payload['checklist_legal'] as $item) {
            fputcsv($out, [
                'legal',
                $item['codigo'],
                $item['titulo'],
                $item['status'],
                $item['evidencia'],
            ], ';');
        }

        foreach ($payload['relatorios_obrigatorios'] as $item) {
            fputcsv($out, [
                'relatorio',
                '',
                $item['nome'],
                $item['status'],
                $item['finalidade'],
            ], ';');
        }

        foreach ($payload['linhas'] as $linha) {
            fputcsv($out, [
                'linha',
                $linha['codigo'],
                $linha['nome'],
                $linha['tipo'],
                $linha['horario'] . ' | ' . $linha['custo'] . ' | pontos=' . (string) ($linha['pontos_total'] ?? '0') . ' | roteiro=' . ($linha['roteiro_resumido'] ?? ''),
            ], ';');
        }

        foreach ($payload['veiculos'] as $veiculo) {
            fputcsv($out, [
                'veiculo',
                $veiculo['placa'],
                $veiculo['modelo'],
                $veiculo['status'],
                'motorista=' . $veiculo['motorista'],
            ], ';');
        }

        foreach ($payload['alunos'] as $aluno) {
            fputcsv($out, [
                'aluno',
                (string) $aluno['cpf'],
                $aluno['nome'],
                $aluno['escola'],
                $aluno['linha'] . ' | ' . $aluno['embarque'] . ' | unidade=' . ($aluno['unidade_escolar'] ?? ''),
            ], ';');
        }

        rewind($out);
        $conteudo = stream_get_contents($out);
        fclose($out);

        return is_string($conteudo) ? $conteudo : '';
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function baseLegal(): array
    {
        return [
            [
                'norma' => 'Controle interno',
                'referencia' => 'Relatorio consolidado de operacao e fiscalizacao da area.',
            ],
            [
                'norma' => 'Educacao',
                'referencia' => 'Lista de alunos, escolas e carteiras emitidas.',
            ],
            [
                'norma' => 'Frota',
                'referencia' => 'Veiculos vinculados, manutencao e situacao operacional.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $filtros
     * @return array<string, string>
     */
    private function normalizarFiltros(array $filtros): array
    {
        return [
            'linha' => trim((string) ($filtros['linha'] ?? '')),
            'periodo' => trim((string) ($filtros['periodo'] ?? '')),
            'escola' => trim((string) ($filtros['escola'] ?? '')),
        ];
    }

    /**
     * @param array<int, array<string, string>> $alunos
     * @param array<string, string> $filtros
     * @return array<int, array<string, string>>
     */
    private function filtrarAlunos(array $alunos, array $filtros): array
    {
        $resultado = [];

        foreach ($alunos as $aluno) {
            if ($filtros['linha'] !== '' && !$this->matchValor((string) ($aluno['linha'] ?? ''), $filtros['linha'])) {
                continue;
            }

            if ($filtros['periodo'] !== '' && !$this->matchValor($aluno['periodo_uso'] ?? '', $filtros['periodo'])) {
                continue;
            }

            if ($filtros['escola'] !== '' && !$this->matchValor($aluno['escola'] ?? '', $filtros['escola'])) {
                continue;
            }

            $resultado[] = $aluno;
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, string>> $alunos
     * @param array<int, array<string, string>> $linhas
     * @return array<int, array<string, string>>
     */
    private function enriquecerAlunos(array $alunos, array $linhas): array
    {
        $linhasPorCodigo = [];
        foreach ($linhas as $linha) {
            if (empty($linha['codigo'])) {
                continue;
            }

            $linhasPorCodigo[$linha['codigo']] = $linha;
        }

        $resultado = [];
        foreach ($alunos as $aluno) {
            $codigoLinha = isset($aluno['linha']) ? (string) $aluno['linha'] : '';
            $unidade = isset($aluno['escola']) ? (string) $aluno['escola'] : '';

            if ($codigoLinha !== '' && isset($linhasPorCodigo[$codigoLinha]) && !empty($linhasPorCodigo[$codigoLinha]['unidade_escolar'])) {
                $unidade = (string) $linhasPorCodigo[$codigoLinha]['unidade_escolar'];
            }

            $aluno['unidade_escolar'] = $unidade;
            $resultado[] = $aluno;
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, string>> $linhas
     * @param array<int, array<string, string>> $alunos
     * @param array<string, string> $filtros
     * @return array<int, array<string, string>>
     */
    private function filtrarLinhas(array $linhas, array $alunos, array $filtros): array
    {
        $linhasPermitidas = [];
        foreach ($alunos as $aluno) {
            if (isset($aluno['linha']) && $aluno['linha'] !== '') {
                $linhasPermitidas[$aluno['linha']] = true;
            }
        }

        $resultado = [];
        foreach ($linhas as $linha) {
            $codigo = isset($linha['codigo']) ? (string) $linha['codigo'] : '';
            $unidade = isset($linha['unidade_escolar']) ? (string) $linha['unidade_escolar'] : '';

            if ($linhasPermitidas !== [] && !isset($linhasPermitidas[$codigo])) {
                continue;
            }

            if ($filtros['escola'] !== '' && !$this->matchValor($unidade, $filtros['escola']) && !$this->matchValor($linha['nome'] ?? '', $filtros['escola'])) {
                continue;
            }

            $resultado[] = $linha;
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, string>> $itens
     * @return array<int, string>
     */
    private function valoresDisponiveis(array $itens, string $chave): array
    {
        $valores = [];
        foreach ($itens as $item) {
            if (!isset($item[$chave])) {
                continue;
            }

            $valor = trim((string) $item[$chave]);
            if ($valor === '') {
                continue;
            }

            $valores[$valor] = $valor;
        }

        $valores = array_values($valores);
        sort($valores, SORT_NATURAL | SORT_FLAG_CASE);

        return $valores;
    }

    /**
     * @param array<string, mixed> $filtros
     * @return array<int, array{label: string, valor: string}>
     */
    private function formatarFiltros(array $filtros): array
    {
        $aplicados = [];

        if (isset($filtros['periodo']) && $filtros['periodo'] !== '') {
            $aplicados[] = [
                'label' => 'Periodo',
                'valor' => $filtros['periodo'],
            ];
        }

        if (isset($filtros['escola']) && $filtros['escola'] !== '') {
            $aplicados[] = [
                'label' => 'Escola',
                'valor' => $filtros['escola'],
            ];
        }

        if (isset($filtros['linha']) && $filtros['linha'] !== '') {
            $aplicados[] = [
                'label' => 'Linha',
                'valor' => $filtros['linha'],
            ];
        }

        return $aplicados;
    }

    /**
     * @param array<int, array<string, string>> $linhas
     * @return array<int, array{codigo: string, nome: string}>
     */
    private function linhasDisponiveis(array $linhas): array
    {
        $resultado = [];

        foreach ($linhas as $linha) {
            $codigo = trim((string) ($linha['codigo'] ?? ''));
            if ($codigo === '') {
                continue;
            }

            $resultado[] = [
                'codigo' => $codigo,
                'nome' => trim((string) ($linha['nome'] ?? '')),
            ];
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, string>> $linhas
     * @param string $linhaFiltro
     * @return array<string, string>
     */
    private function linhaSelecionada(array $linhas, string $linhaFiltro): array
    {
        if ($linhaFiltro === '') {
            return [];
        }

        foreach ($linhas as $linha) {
            $codigo = trim((string) ($linha['codigo'] ?? ''));
            if ($codigo === '') {
                continue;
            }

            if (stripos($codigo, $linhaFiltro) !== false) {
                return [
                    'codigo' => $codigo,
                    'nome' => trim((string) ($linha['nome'] ?? '')),
                    'pontos_total' => (int) ($linha['pontos_total'] ?? 0),
                    'roteiro_resumido' => trim((string) ($linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? ''))),
                ];
            }
        }

        return [];
    }

    private function matchValor(string $valor, string $filtro): bool
    {
        if ($filtro === '') {
            return true;
        }

        if ($valor === '') {
            return false;
        }

        return stripos($valor, $filtro) !== false;
    }

    /**
     * @param array<int, array<string, string>> $items
     * @return array<string, int>
     */
    private function contagemPorChave(array $items, string $key): array
    {
        $contagem = [];

        foreach ($items as $item) {
            $valor = isset($item[$key]) && $item[$key] !== '' ? (string) $item[$key] : 'nao_informado';
            if (!isset($contagem[$valor])) {
                $contagem[$valor] = 0;
            }
            $contagem[$valor]++;
        }

        ksort($contagem);

        return $contagem;
    }
}
