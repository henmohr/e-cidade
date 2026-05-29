<?php

namespace App\Services\Educacao\TransporteEscolar;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaVeiculoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\PontoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\VeiculoTransporteEscolar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

class TransporteEscolarSeteService
{
    private TransporteEscolarExportService $exportService;

    public function __construct(TransporteEscolarExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * @return array<string, mixed>
     */
    public function exportar(array $filtros = []): array
    {
        $base = $this->exportService->payload($filtros);

        return [
            'metadados' => [
                'gerado_em' => date('c'),
                'fonte' => 'educacao_transporte_escolar',
                'formato' => 'sete-json',
                'versao' => 'v1',
            ],
            'linhas' => $this->mapearLinhasParaSete($base['linhas'] ?? []),
            'veiculos' => $this->mapearVeiculosParaSete($base['veiculos'] ?? []),
            'pontos' => $this->mapearPontosParaSete($base['pontos'] ?? []),
            'vinculos' => $this->mapearVinculosParaSete($base['linhas'] ?? []),
            'alunos' => $this->mapearAlunosParaSete($base['alunos'] ?? []),
        ];
    }

    public function exportarJson(array $filtros = []): string
    {
        $conteudo = json_encode($this->exportar($filtros), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!is_string($conteudo)) {
            return '{}';
        }

        return $conteudo;
    }

    /**
     * @return array<string, mixed>
     */
    public function importarJson(string $conteudo): array
    {
        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) {
            throw new InvalidArgumentException('Arquivo SETE invalido ou nao eh JSON valido.');
        }

        $linhas = $this->garantirLista($dados['linhas'] ?? []);
        $veiculos = $this->garantirLista($dados['veiculos'] ?? []);
        $pontos = $this->garantirLista($dados['pontos'] ?? []);
        $vinculos = $this->garantirLista($dados['vinculos'] ?? []);
        $alunos = $this->garantirLista($dados['alunos'] ?? []);

        $this->garantirEstrutura();

        $resumo = [
            'linhas_criadas' => 0,
            'linhas_atualizadas' => 0,
            'veiculos_criados' => 0,
            'veiculos_atualizados' => 0,
            'pontos_criados' => 0,
            'pontos_atualizados' => 0,
            'vinculos_criados' => 0,
            'vinculos_atualizados' => 0,
            'alunos_criados' => 0,
            'alunos_atualizados' => 0,
        ];

        DB::transaction(function () use ($linhas, $veiculos, $pontos, $vinculos, $alunos, &$resumo): void {
            $linhasPorCodigo = [];
            $veiculosPorPlaca = [];

            foreach ($linhas as $linhaDados) {
                $codigo = trim((string) ($linhaDados['codigo'] ?? ''));
                if ($codigo === '') {
                    throw new InvalidArgumentException('Linha SETE sem codigo.');
                }

                $existeAntes = $this->linhaExiste($codigo);
                $linha = $this->salvarLinhaSete($linhaDados);
                $linhasPorCodigo[$linha->codigo] = $linha;

                if ($existeAntes) {
                    $resumo['linhas_atualizadas']++;
                } else {
                    $resumo['linhas_criadas']++;
                }
            }

            foreach ($veiculos as $veiculoDados) {
                $placa = trim((string) ($veiculoDados['placa'] ?? ''));
                if ($placa === '') {
                    throw new InvalidArgumentException('Veiculo SETE sem placa.');
                }

                $existeAntes = $this->veiculoExiste($placa);
                $veiculo = $this->salvarVeiculoSete($veiculoDados);
                $veiculosPorPlaca[$veiculo->placa] = $veiculo;

                if ($existeAntes) {
                    $resumo['veiculos_atualizados']++;
                } else {
                    $resumo['veiculos_criados']++;
                }
            }

            foreach ($pontos as $pontoDados) {
                $existeAntes = $this->pontoExiste($pontoDados, $linhasPorCodigo);
                $this->salvarPontoSete($pontoDados, $linhasPorCodigo);

                if ($existeAntes) {
                    $resumo['pontos_atualizados']++;
                } else {
                    $resumo['pontos_criados']++;
                }
            }

            foreach ($vinculos as $vinculoDados) {
                $linhaCodigo = trim((string) ($vinculoDados['linha_codigo'] ?? ''));
                $veiculoPlaca = trim((string) ($vinculoDados['veiculo_placa'] ?? ''));

                $existeAntes = $this->vinculoExiste($linhaCodigo, $veiculoPlaca, $linhasPorCodigo, $veiculosPorPlaca);
                $this->salvarVinculoSete($vinculoDados, $linhasPorCodigo, $veiculosPorPlaca);

                if ($existeAntes) {
                    $resumo['vinculos_atualizados']++;
                } else {
                    $resumo['vinculos_criados']++;
                }
            }

            foreach ($alunos as $alunoDados) {
                $existeAntes = $this->alunoExiste($alunoDados);
                $this->salvarAlunoSete($alunoDados, $linhasPorCodigo);

                if ($existeAntes) {
                    $resumo['alunos_atualizados']++;
                } else {
                    $resumo['alunos_criados']++;
                }
            }
        });

        return $resumo;
    }

    /**
     * @param array<int, array<string, mixed>> $linhas
     * @return array<int, array<string, mixed>>
     */
    private function mapearLinhasParaSete(array $linhas): array
    {
        $resultado = [];

        foreach ($linhas as $linha) {
            $horario = isset($linha['horario']) ? (string) $linha['horario'] : '';
            $resultado[] = [
                'codigo' => (string) ($linha['codigo'] ?? ''),
                'nome' => (string) ($linha['nome'] ?? ''),
                'tipo_servico' => (string) ($linha['tipo'] ?? ($linha['tipo_servico'] ?? '')),
                'horario_saida' => $this->separarHorario($horario, 0),
                'horario_retorno' => $this->separarHorario($horario, 1),
                'custo_mensal' => $this->custoParaNumero((string) ($linha['custo'] ?? '0')),
                'unidade_escolar' => (string) ($linha['unidade_escolar'] ?? ''),
                'rota_descricao' => (string) ($linha['rota_descricao'] ?? ($linha['roteiro_resumido'] ?? ($linha['nome'] ?? ''))),
                'pontos_total' => (int) ($linha['pontos_total'] ?? 0),
                'roteiro_resumido' => (string) ($linha['roteiro_resumido'] ?? ($linha['rota_descricao'] ?? ($linha['nome'] ?? ''))),
                'ativo' => !isset($linha['ativo']) || (bool) $linha['ativo'],
            ];
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, mixed>> $veiculos
     * @return array<int, array<string, mixed>>
     */
    private function mapearVeiculosParaSete(array $veiculos): array
    {
        $resultado = [];

        foreach ($veiculos as $veiculo) {
            $resultado[] = [
                'placa' => (string) ($veiculo['placa'] ?? ''),
                'modelo' => (string) ($veiculo['modelo'] ?? ''),
                'motorista_nome' => (string) ($veiculo['motorista'] ?? ($veiculo['motorista_nome'] ?? '')),
                'capacidade' => isset($veiculo['capacidade']) ? (int) $veiculo['capacidade'] : null,
                'situacao' => (string) ($veiculo['status'] ?? ($veiculo['situacao'] ?? 'disponivel')),
                'observacao' => (string) ($veiculo['observacao'] ?? ''),
            ];
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, mixed>> $pontos
     * @return array<int, array<string, mixed>>
     */
    private function mapearPontosParaSete(array $pontos): array
    {
        $resultado = [];

        foreach ($pontos as $ponto) {
            $resultado[] = [
                'linha_codigo' => (string) ($ponto['linha_codigo'] ?? ''),
                'nome' => (string) ($ponto['nome'] ?? ''),
                'endereco' => (string) ($ponto['endereco'] ?? ''),
                'tipo_ponto' => (string) ($ponto['tipo_ponto'] ?? ($ponto['tipo'] ?? 'parada')),
                'ordem' => (int) ($ponto['ordem'] ?? 0),
                'observacao' => (string) ($ponto['observacao'] ?? ''),
                'ativo' => !isset($ponto['ativo']) || (bool) $ponto['ativo'],
            ];
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, mixed>> $linhas
     * @return array<int, array<string, mixed>>
     */
    private function mapearVinculosParaSete(array $linhas): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_linha_veiculos')) {
            return $this->fallbackVinculos($linhas);
        }

        $codigosPermitidos = $this->codigosPorLinhas($linhas);
        $resultado = [];

        $vinculos = LinhaVeiculoTransporteEscolar::query()
            ->with(['linha', 'veiculo'])
            ->orderBy('id')
            ->get();

        foreach ($vinculos as $vinculo) {
            $linha = $vinculo->linha;
            $veiculo = $vinculo->veiculo;

            if ($linha === null || $veiculo === null) {
                continue;
            }

            if ($codigosPermitidos !== [] && !isset($codigosPermitidos[$linha->codigo])) {
                continue;
            }

            $resultado[] = [
                'linha_codigo' => (string) $linha->codigo,
                'linha_nome' => (string) $linha->nome,
                'veiculo_placa' => (string) $veiculo->placa,
                'veiculo_modelo' => (string) $veiculo->modelo,
                'data_inicio' => $this->dataComoTexto($vinculo->data_inicio),
                'data_fim' => $this->dataComoTexto($vinculo->data_fim),
                'observacao' => (string) ($vinculo->observacao ?? ''),
            ];
        }

        return $resultado;
    }

    /**
     * @param array<int, array<string, mixed>> $alunos
     * @return array<int, array<string, mixed>>
     */
    private function mapearAlunosParaSete(array $alunos): array
    {
        $resultado = [];

        foreach ($alunos as $aluno) {
            $resultado[] = [
                'aluno_cpf' => (string) ($aluno['cpf'] ?? ''),
                'aluno_nome' => (string) ($aluno['nome'] ?? ''),
                'escola_nome' => (string) ($aluno['escola'] ?? ''),
                'local_embarque' => (string) ($aluno['embarque'] ?? ''),
                'motivo_uso' => (string) ($aluno['motivo_uso'] ?? 'Transporte escolar'),
                'periodo_uso' => (string) ($aluno['periodo_uso'] ?? ''),
                'situacao_matricula' => (string) ($aluno['situacao_matricula'] ?? 'Ativo'),
                'utiliza_transporte' => isset($aluno['utiliza_transporte']) ? (bool) $aluno['utiliza_transporte'] : true,
                'linha_codigo' => (string) ($aluno['linha'] ?? ''),
                'unidade_escolar' => (string) ($aluno['unidade_escolar'] ?? ''),
            ];
        }

        return $resultado;
    }

    /**
     * @param array<string, mixed> $dados
     */
    private function salvarLinhaSete(array $dados): LinhaTransporteEscolar
    {
        $linha = LinhaTransporteEscolar::query()->firstOrNew([
            'codigo' => (string) ($dados['codigo'] ?? ''),
        ]);

        $linha->fill([
            'nome' => $dados['nome'] ?? '',
            'tipo_servico' => $dados['tipo_servico'] ?? 'proprio',
            'horario_saida' => $dados['horario_saida'] ?? null,
            'horario_retorno' => $dados['horario_retorno'] ?? null,
            'custo_mensal' => $dados['custo_mensal'] ?? 0,
            'unidade_escolar' => $dados['unidade_escolar'] ?? null,
            'rota_descricao' => $dados['rota_descricao'] ?? null,
            'ativo' => (bool) ($dados['ativo'] ?? true),
        ]);
        $linha->save();

        return $linha->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     */
    private function salvarVeiculoSete(array $dados): VeiculoTransporteEscolar
    {
        $veiculo = VeiculoTransporteEscolar::query()->firstOrNew([
            'placa' => (string) ($dados['placa'] ?? ''),
        ]);

        $veiculo->fill([
            'modelo' => $dados['modelo'] ?? '',
            'motorista_nome' => $dados['motorista_nome'] ?? null,
            'capacidade' => $dados['capacidade'] ?? null,
            'situacao' => $dados['situacao'] ?? 'disponivel',
            'observacao' => $dados['observacao'] ?? null,
        ]);
        $veiculo->save();

        return $veiculo->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, LinhaTransporteEscolar> $linhasPorCodigo
     */
    private function salvarPontoSete(array $dados, array $linhasPorCodigo): PontoTransporteEscolar
    {
        $linhaCodigo = trim((string) ($dados['linha_codigo'] ?? ''));
        $linha = $this->resolverLinhaPorCodigo($linhaCodigo, $linhasPorCodigo);
        if ($linha === null) {
            throw new InvalidArgumentException('Linha invalida para ponto SETE.');
        }

        $ponto = PontoTransporteEscolar::query()->firstOrNew([
            'linha_id' => $linha->id,
            'nome' => (string) ($dados['nome'] ?? ''),
            'ordem' => (int) ($dados['ordem'] ?? 0),
        ]);

        $ponto->fill([
            'endereco' => $dados['endereco'] ?? null,
            'tipo_ponto' => $dados['tipo_ponto'] ?? 'parada',
            'observacao' => $dados['observacao'] ?? null,
            'ativo' => (bool) ($dados['ativo'] ?? true),
        ]);
        $ponto->save();

        return $ponto->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, LinhaTransporteEscolar> $linhasPorCodigo
     * @param array<string, VeiculoTransporteEscolar> $veiculosPorPlaca
     */
    private function salvarVinculoSete(array $dados, array $linhasPorCodigo, array $veiculosPorPlaca): LinhaVeiculoTransporteEscolar
    {
        $linhaCodigo = trim((string) ($dados['linha_codigo'] ?? ''));
        $veiculoPlaca = trim((string) ($dados['veiculo_placa'] ?? ''));

        $linha = $this->resolverLinhaPorCodigo($linhaCodigo, $linhasPorCodigo);
        $veiculo = $this->resolverVeiculoPorPlaca($veiculoPlaca, $veiculosPorPlaca);

        if ($linha === null) {
            throw new InvalidArgumentException('Linha invalida para vinculo SETE.');
        }

        if ($veiculo === null) {
            throw new InvalidArgumentException('Veiculo invalido para vinculo SETE.');
        }

        $vinculo = LinhaVeiculoTransporteEscolar::query()->firstOrNew([
            'linha_id' => $linha->id,
            'veiculo_id' => $veiculo->id,
        ]);

        $vinculo->fill([
            'data_inicio' => $dados['data_inicio'] ?? null,
            'data_fim' => $dados['data_fim'] ?? null,
            'observacao' => $dados['observacao'] ?? null,
        ]);
        $vinculo->save();

        return $vinculo->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, LinhaTransporteEscolar> $linhasPorCodigo
     */
    private function salvarAlunoSete(array $dados, array $linhasPorCodigo): AlunoTransporteEscolar
    {
        $linhaCodigo = trim((string) ($dados['linha_codigo'] ?? ''));
        $linha = $this->resolverLinhaPorCodigo($linhaCodigo, $linhasPorCodigo);
        $linhaId = $linha !== null ? $linha->id : null;

        $aluno = AlunoTransporteEscolar::query()->firstOrNew([
            'aluno_cpf' => (string) ($dados['aluno_cpf'] ?? ''),
            'aluno_nome' => (string) ($dados['aluno_nome'] ?? ''),
            'escola_nome' => (string) ($dados['escola_nome'] ?? ''),
        ]);

        $aluno->fill([
            'linha_id' => $linhaId,
            'cgm_id' => $dados['cgm_id'] ?? null,
            'local_embarque' => $dados['local_embarque'] ?? null,
            'motivo_uso' => $dados['motivo_uso'] ?? null,
            'periodo_uso' => $dados['periodo_uso'] ?? null,
            'situacao_matricula' => $dados['situacao_matricula'] ?? null,
            'utiliza_transporte' => (bool) ($dados['utiliza_transporte'] ?? true),
            'foto_path' => $dados['foto_path'] ?? null,
        ]);
        $aluno->save();

        return $aluno->refresh();
    }

    private function linhaExiste(string $codigo): bool
    {
        return $codigo !== '' && LinhaTransporteEscolar::query()->where('codigo', $codigo)->exists();
    }

    private function veiculoExiste(string $placa): bool
    {
        return $placa !== '' && VeiculoTransporteEscolar::query()->where('placa', $placa)->exists();
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, LinhaTransporteEscolar> $linhasPorCodigo
     */
    private function pontoExiste(array $dados, array $linhasPorCodigo): bool
    {
        $linhaCodigo = trim((string) ($dados['linha_codigo'] ?? ''));
        $linha = $this->resolverLinhaPorCodigo($linhaCodigo, $linhasPorCodigo);
        $nome = trim((string) ($dados['nome'] ?? ''));
        $ordem = (int) ($dados['ordem'] ?? 0);

        if ($linha === null || $nome === '') {
            return false;
        }

        return PontoTransporteEscolar::query()
            ->where('linha_id', $linha->id)
            ->where('nome', $nome)
            ->where('ordem', $ordem)
            ->exists();
    }

    /**
     * @param array<string, mixed> $dados
     * @param array<string, LinhaTransporteEscolar> $linhasPorCodigo
     * @param array<string, VeiculoTransporteEscolar> $veiculosPorPlaca
     */
    private function vinculoExiste(string $linhaCodigo, string $veiculoPlaca, array $linhasPorCodigo, array $veiculosPorPlaca): bool
    {
        $linha = $this->resolverLinhaPorCodigo($linhaCodigo, $linhasPorCodigo);
        $veiculo = $this->resolverVeiculoPorPlaca($veiculoPlaca, $veiculosPorPlaca);

        if ($linha === null || $veiculo === null) {
            return false;
        }

        return LinhaVeiculoTransporteEscolar::query()
            ->where('linha_id', $linha->id)
            ->where('veiculo_id', $veiculo->id)
            ->exists();
    }

    /**
     * @param array<string, mixed> $dados
     */
    private function alunoExiste(array $dados): bool
    {
        $cpf = trim((string) ($dados['aluno_cpf'] ?? ''));
        $nome = trim((string) ($dados['aluno_nome'] ?? ''));
        $escola = trim((string) ($dados['escola_nome'] ?? ''));

        if ($cpf !== '') {
            return AlunoTransporteEscolar::query()->where('aluno_cpf', $cpf)->exists();
        }

        if ($nome === '' || $escola === '') {
            return false;
        }

        return AlunoTransporteEscolar::query()
            ->where('aluno_nome', $nome)
            ->where('escola_nome', $escola)
            ->exists();
    }

    /**
     * @param array<string, mixed> $dados
     * @return array<int, mixed>
     */
    private function garantirLista($dados): array
    {
        return is_array($dados) ? $dados : [];
    }

    private function garantirEstrutura(): void
    {
        foreach ([
            'educacao_transporte_escolar_linhas',
            'educacao_transporte_escolar_veiculos',
            'educacao_transporte_escolar_pontos',
            'educacao_transporte_escolar_linha_veiculos',
            'educacao_transporte_escolar_alunos',
        ] as $tabela) {
            if (!$this->hasTable($tabela)) {
                throw new InvalidArgumentException('Estrutura do transporte escolar nao esta instalada.');
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $linhas
     * @return array<string, true>
     */
    private function codigosPorLinhas(array $linhas): array
    {
        $resultado = [];

        foreach ($linhas as $linha) {
            $codigo = trim((string) ($linha['codigo'] ?? ''));
            if ($codigo === '') {
                continue;
            }

            $resultado[$codigo] = true;
        }

        return $resultado;
    }

    /**
     * @param array<string, mixed> $linhas
     * @return array<int, array<string, mixed>>
     */
    private function fallbackVinculos(array $linhas): array
    {
        $veiculos = $this->exportService->payload([])['veiculos'] ?? [];
        $resultado = [];

        foreach ($linhas as $indice => $linha) {
            if (!isset($veiculos[$indice])) {
                continue;
            }

            $veiculo = $veiculos[$indice];
            $resultado[] = [
                'linha_codigo' => (string) ($linha['codigo'] ?? ''),
                'linha_nome' => (string) ($linha['nome'] ?? ''),
                'veiculo_placa' => (string) ($veiculo['placa'] ?? ''),
                'veiculo_modelo' => (string) ($veiculo['modelo'] ?? ''),
                'data_inicio' => '',
                'data_fim' => '',
                'observacao' => '',
            ];
        }

        return $resultado;
    }

    private function resolverLinhaPorCodigo(string $codigo, array $linhasPorCodigo): ?LinhaTransporteEscolar
    {
        if ($codigo !== '' && isset($linhasPorCodigo[$codigo])) {
            return $linhasPorCodigo[$codigo];
        }

        if ($codigo === '' || !$this->hasTable('educacao_transporte_escolar_linhas')) {
            return null;
        }

        $linha = LinhaTransporteEscolar::query()->where('codigo', $codigo)->first();
        if ($linha !== null) {
            return $linha;
        }

        return null;
    }

    private function resolverVeiculoPorPlaca(string $placa, array $veiculosPorPlaca): ?VeiculoTransporteEscolar
    {
        if ($placa !== '' && isset($veiculosPorPlaca[$placa])) {
            return $veiculosPorPlaca[$placa];
        }

        if ($placa === '' || !$this->hasTable('educacao_transporte_escolar_veiculos')) {
            return null;
        }

        $veiculo = VeiculoTransporteEscolar::query()->where('placa', $placa)->first();
        if ($veiculo !== null) {
            return $veiculo;
        }

        return null;
    }

    private function separarHorario(string $horario, int $parte): string
    {
        $pedacos = array_map('trim', explode('/', $horario));

        if (!isset($pedacos[$parte]) || $pedacos[$parte] === '') {
            return '';
        }

        return $pedacos[$parte];
    }

    private function custoParaNumero(string $texto): float
    {
        $limpo = str_replace(['R$', '.', ','], ['', '', '.'], $texto);

        return (float) $limpo;
    }

    /**
     * @param mixed $data
     */
    private function dataComoTexto($data): string
    {
        if ($data === null) {
            return '';
        }

        if (is_string($data)) {
            return $data;
        }

        if ($data instanceof \DateTimeInterface) {
            return $data->format('Y-m-d');
        }

        return (string) $data;
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $e) {
            return false;
        }
    }
}
