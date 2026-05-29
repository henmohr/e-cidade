<?php

namespace App\Services\Educacao\TransporteEscolar;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\PontoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\VeiculoTransporteEscolar;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TransporteEscolarExportService
{
    /**
     * @return array<string, mixed>
     */
    public function payload(array $filtros = []): array
    {
        $filtros = $this->normalizarFiltros($filtros);
        $linhasBase = $this->linhas();
        $veiculos = $this->veiculos();
        $pontos = $this->pontos();
        $alunosBase = $this->alunos();

        $alunos = $this->enriquecerAlunos($this->filtrarAlunos($alunosBase, $filtros), $linhasBase);
        $linhas = $this->filtrarLinhas($linhasBase, $alunos, $filtros);

        return [
            'linhas' => $linhas,
            'pontos' => $pontos,
            'veiculos' => $veiculos,
            'alunos' => $alunos,
            'filtros' => $filtros,
            'filtros_aplicados' => $this->formatarFiltros($filtros),
            'filtro_descricao' => $this->descricaoFiltros($filtros),
            'linhas_disponiveis' => $this->linhasDisponiveis($linhasBase),
            'escolas_disponiveis' => $this->valoresDisponiveis($alunosBase, 'escola'),
            'periodos_disponiveis' => $this->valoresDisponiveis($alunosBase, 'periodo_uso'),
            'metadados' => [
                'gerado_em' => date('c'),
                'fonte' => 'educacao_transporte_escolar',
                'versao' => 'v1',
            ],
        ];
    }

    public function csv(array $filtros = []): string
    {
        $payload = $this->payload($filtros);
        $out = fopen('php://temp', 'w+');
        if ($out === false) {
            return '';
        }

        fputcsv($out, ['secao', 'codigo', 'nome', 'tipo', 'extra'], ';');

        foreach ($payload['filtros_aplicados'] as $item) {
            fputcsv($out, [
                'filtro',
                '',
                $item['label'],
                'aplicado',
                $item['valor'],
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

        foreach ($payload['pontos'] as $ponto) {
            fputcsv($out, [
                'ponto',
                $ponto['linha_codigo'] ?? '',
                $ponto['nome'],
                $ponto['tipo_ponto'],
                $ponto['endereco'] . ' | ordem=' . $ponto['ordem'],
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
     * @return array<int, array<string, string>>
     */
    private function linhas(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_linhas')) {
            return $this->fallbackLinhas();
        }

        $linhas = LinhaTransporteEscolar::query()
            ->with('pontos')
            ->orderBy('codigo')
            ->get()
            ->map(function (LinhaTransporteEscolar $linha): array {
                return array_merge([
                    'codigo' => $linha->codigo,
                    'nome' => $linha->nome,
                    'tipo' => $linha->tipo_servico,
                    'unidade_escolar' => $linha->unidade_escolar ?? '',
                    'horario' => trim(($linha->horario_saida ?? '--') . ' / ' . ($linha->horario_retorno ?? '--')),
                    'custo' => 'R$ ' . number_format((float) $linha->custo_mensal, 2, ',', '.'),
                ], $this->resumirLinha($linha));
            })
            ->all();

        return $linhas !== [] ? $linhas : $this->fallbackLinhas();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function veiculos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_veiculos')) {
            return $this->fallbackVeiculos();
        }

        $veiculos = VeiculoTransporteEscolar::query()
            ->orderBy('placa')
            ->get()
            ->map(static function (VeiculoTransporteEscolar $veiculo): array {
                return [
                    'placa' => $veiculo->placa,
                    'modelo' => $veiculo->modelo,
                    'motorista' => $veiculo->motorista_nome ?? 'Nao informado',
                    'status' => $veiculo->situacao,
                ];
            })
            ->all();

        return $veiculos !== [] ? $veiculos : $this->fallbackVeiculos();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function pontos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_pontos')) {
            return $this->fallbackPontos();
        }

        $pontos = PontoTransporteEscolar::query()
            ->with('linha')
            ->orderBy('linha_id')
            ->orderBy('ordem')
            ->get()
            ->map(static function (PontoTransporteEscolar $ponto): array {
                return [
                    'linha_codigo' => $ponto->linha ? $ponto->linha->codigo : '',
                    'nome' => $ponto->nome,
                    'endereco' => $ponto->endereco ?? '',
                    'tipo_ponto' => $ponto->tipo_ponto,
                    'ordem' => (string) $ponto->ordem,
                    'observacao' => $ponto->observacao ?? '',
                ];
            })
            ->all();

        return $pontos !== [] ? $pontos : $this->fallbackPontos();
    }

    /**
     * @return array<int, PontoTransporteEscolar>
     */
    private function pontosDaLinha(LinhaTransporteEscolar $linha): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_pontos')) {
            return [];
        }

        $pontos = $linha->relationLoaded('pontos') ? $linha->getRelation('pontos') : $linha->pontos()->orderBy('ordem')->get();
        if ($pontos === null) {
            return [];
        }

        $resultado = [];
        foreach ($pontos as $ponto) {
            if (!$ponto instanceof PontoTransporteEscolar) {
                continue;
            }

            if (!$ponto->ativo) {
                continue;
            }

            $resultado[] = $ponto;
        }

        return $resultado;
    }

    /**
     * @param array<int, PontoTransporteEscolar> $pontos
     * @return array{resumido: string, detalhado: array<int, string>}
     */
    private function roteiroDaLinha(array $pontos): array
    {
        $detalhado = [];
        $nomes = [];

        foreach ($pontos as $indice => $ponto) {
            $partes = [];

            $nome = trim((string) $ponto->nome);
            if ($nome !== '') {
                $partes[] = $nome;
                $nomes[] = $nome;
            }

            $endereco = trim((string) ($ponto->endereco ?? ''));
            if ($endereco !== '') {
                $partes[] = $endereco;
            }

            $tipo = trim((string) ($ponto->tipo_ponto ?? ''));
            if ($tipo !== '') {
                $partes[] = $tipo;
            }

            $texto = implode(' - ', $partes);
            if ($texto === '') {
                $texto = 'Ponto #' . ($indice + 1);
            }

            $detalhado[] = ($indice + 1) . '. ' . $texto;
        }

        return [
            'resumido' => $nomes !== [] ? implode(' -> ', $nomes) : '',
            'detalhado' => $detalhado,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resumirLinha(LinhaTransporteEscolar $linha): array
    {
        $pontos = $this->pontosDaLinha($linha);
        $roteiro = $this->roteiroDaLinha($pontos);
        $resumido = trim($roteiro['resumido']);

        if ($resumido === '') {
            $resumido = trim((string) ($linha->rota_descricao ?? ''));
        }

        if ($resumido === '') {
            $resumido = 'Roteiro sem pontos cadastrados';
        }

        return [
            'pontos_total' => count($pontos),
            'roteiro_resumido' => $resumido,
            'roteiro_detalhado' => $roteiro['detalhado'],
            'rota_descricao' => (string) ($linha->rota_descricao ?? ''),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function alunos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_alunos')) {
            return $this->fallbackAlunos();
        }

        $alunos = AlunoTransporteEscolar::query()
            ->orderBy('aluno_nome')
            ->get()
            ->map(static function (AlunoTransporteEscolar $aluno): array {
                return [
                    'cpf' => $aluno->aluno_cpf ?? '',
                    'nome' => $aluno->aluno_nome,
                    'escola' => $aluno->escola_nome ?? '',
                    'linha' => $aluno->linha ? $aluno->linha->codigo : '',
                    'embarque' => $aluno->local_embarque ?? '',
                    'periodo_uso' => $aluno->periodo_uso ?? '',
                ];
            })
            ->all();

        return $alunos !== [] ? $alunos : $this->fallbackAlunos();
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

            if ($filtros['periodo'] !== '' && stripos((string) ($aluno['periodo_uso'] ?? ''), $filtros['periodo']) === false) {
                continue;
            }

            if ($filtros['escola'] !== '' && stripos((string) ($aluno['escola'] ?? ''), $filtros['escola']) === false) {
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
        $codigosPermitidos = [];
        foreach ($alunos as $aluno) {
            if (!empty($aluno['linha'])) {
                $codigosPermitidos[$aluno['linha']] = true;
            }
        }

        $resultado = [];
        foreach ($linhas as $linha) {
            $codigo = isset($linha['codigo']) ? (string) $linha['codigo'] : '';
            $unidade = isset($linha['unidade_escolar']) ? (string) $linha['unidade_escolar'] : '';
            $nome = isset($linha['nome']) ? (string) $linha['nome'] : '';

            if ($codigosPermitidos !== [] && !isset($codigosPermitidos[$codigo])) {
                continue;
            }

            if ($filtros['escola'] !== '' && stripos($unidade, $filtros['escola']) === false && stripos($nome, $filtros['escola']) === false) {
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
            $valor = trim((string) ($item[$chave] ?? ''));
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
     * @param array<string, string> $filtros
     * @return array<int, array{label: string, valor: string}>
     */
    private function formatarFiltros(array $filtros): array
    {
        $aplicados = [];

        if ($filtros['periodo'] !== '') {
            $aplicados[] = ['label' => 'Periodo', 'valor' => $filtros['periodo']];
        }

        if ($filtros['escola'] !== '') {
            $aplicados[] = ['label' => 'Escola', 'valor' => $filtros['escola']];
        }

        if ($filtros['linha'] !== '') {
            $aplicados[] = ['label' => 'Linha', 'valor' => $filtros['linha']];
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
     * @param array<string, string> $filtros
     */
    private function descricaoFiltros(array $filtros): string
    {
        $aplicados = $this->formatarFiltros($filtros);
        if ($aplicados === []) {
            return 'Sem filtros aplicados';
        }

        $partes = [];
        foreach ($aplicados as $item) {
            $partes[] = $item['label'] . ': ' . $item['valor'];
        }

        return implode(' / ', $partes);
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackLinhas(): array
    {
        return [
            ['codigo' => 'TRE-01', 'nome' => 'Linha Centro - EMEI Esperanca', 'tipo' => 'proprio', 'unidade_escolar' => 'EMEI Esperanca', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 3.420,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Central -> Escola Base'],
            ['codigo' => 'TRE-02', 'nome' => 'Linha Rural - EMEF Vila Nova', 'tipo' => 'terceirizado', 'unidade_escolar' => 'EMEF Vila Nova', 'horario' => '05:55 / 12:10', 'custo' => 'R$ 8.150,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Rural -> Terminal'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackVeiculos(): array
    {
        return [
            ['placa' => 'RIO1A23', 'modelo' => 'Microonibus 28 lugares', 'motorista' => 'Carlos Henrique', 'status' => 'disponivel'],
            ['placa' => 'QWE4Z66', 'modelo' => 'Onibus escolar 44 lugares', 'motorista' => 'Marcos Vinicius', 'status' => 'em_rota'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackPontos(): array
    {
        return [
            ['linha_codigo' => 'TRE-01', 'nome' => 'Ponto Central', 'endereco' => 'Rua Central, 101', 'tipo_ponto' => 'parada', 'ordem' => '1', 'observacao' => 'Referencia centro'],
            ['linha_codigo' => 'TRE-02', 'nome' => 'Ponto Rural', 'endereco' => 'Rodovia Rural km 7', 'tipo_ponto' => 'parada', 'ordem' => '1', 'observacao' => 'Referencia rural'],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackAlunos(): array
    {
        return [
            ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEI Esperanca', 'linha' => 'TRE-01', 'embarque' => 'Rua Central, 101', 'periodo_uso' => 'Manha'],
            ['cpf' => '98765432100', 'nome' => 'Lucas Pereira', 'escola' => 'EMEF Vila Nova', 'linha' => 'TRE-02', 'embarque' => 'Rodovia Rural km 7', 'periodo_uso' => 'Tarde'],
        ];
    }
}
