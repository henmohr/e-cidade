<?php

namespace App\Services\Educacao\TransporteEscolar;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaVeiculoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\PontoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\VeiculoTransporteEscolar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

class TransporteEscolarAdminService
{
    /**
     * @return array<string, mixed>
     */
    public function listagem(): array
    {
        return [
            'linhas' => $this->linhas(),
            'pontos' => $this->pontos(),
            'veiculos' => $this->veiculos(),
            'vinculos' => $this->vinculos(),
            'alunos' => $this->alunos(),
        ];
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function salvarLinha(array $dados): LinhaTransporteEscolar
    {
        $this->assertSchemaDisponivel();

        $id = isset($dados['id']) ? (int) $dados['id'] : null;
        unset($dados['id']);

        /** @var LinhaTransporteEscolar $linha */
        $linha = $id !== null
            ? LinhaTransporteEscolar::query()->findOrFail($id)
            : new LinhaTransporteEscolar();

        $linha->fill($dados);
        $linha->save();

        return $linha->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function salvarVeiculo(array $dados): VeiculoTransporteEscolar
    {
        $this->assertSchemaDisponivel();

        $id = isset($dados['id']) ? (int) $dados['id'] : null;
        unset($dados['id']);

        /** @var VeiculoTransporteEscolar $veiculo */
        $veiculo = $id !== null
            ? VeiculoTransporteEscolar::query()->findOrFail($id)
            : new VeiculoTransporteEscolar();

        $veiculo->fill($dados);
        $veiculo->save();

        return $veiculo->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function salvarPonto(array $dados): PontoTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        $this->assertPontosDisponiveis();

        $id = isset($dados['id']) ? (int) $dados['id'] : null;
        unset($dados['id']);

        /** @var PontoTransporteEscolar $ponto */
        $ponto = $id !== null
            ? PontoTransporteEscolar::query()->findOrFail($id)
            : new PontoTransporteEscolar();

        $ponto->fill($dados);
        $ponto->save();

        return $ponto->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function salvarAluno(array $dados): AlunoTransporteEscolar
    {
        $this->assertSchemaDisponivel();

        $id = isset($dados['id']) ? (int) $dados['id'] : null;
        unset($dados['id']);
        $fotoPath = $dados['foto_path'] ?? null;
        unset($dados['foto_path']);

        /** @var AlunoTransporteEscolar $aluno */
        $aluno = $id !== null
            ? AlunoTransporteEscolar::query()->findOrFail($id)
            : new AlunoTransporteEscolar();

        $fotoAntiga = $aluno->foto_path;
        $aluno->fill($dados);
        $aluno->save();

        if (is_string($fotoPath) && $fotoPath !== '') {
            $aluno->foto_path = $fotoPath;
            $aluno->save();

            if ($fotoAntiga && $fotoAntiga !== $fotoPath && Storage::disk('public')->exists($fotoAntiga)) {
                Storage::disk('public')->delete($fotoAntiga);
            }
        }

        return $aluno->refresh();
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function salvarVinculo(array $dados): LinhaVeiculoTransporteEscolar
    {
        $this->assertSchemaDisponivel();

        $id = isset($dados['id']) ? (int) $dados['id'] : null;
        unset($dados['id']);

        /** @var LinhaVeiculoTransporteEscolar $vinculo */
        $vinculo = $id !== null
            ? LinhaVeiculoTransporteEscolar::query()->findOrFail($id)
            : new LinhaVeiculoTransporteEscolar();

        $vinculo->fill($dados);
        $vinculo->save();

        return $vinculo->refresh();
    }

    public function removerLinha(int $id): void
    {
        $this->assertSchemaDisponivel();
        LinhaTransporteEscolar::query()->whereKey($id)->delete();
    }

    public function removerVeiculo(int $id): void
    {
        $this->assertSchemaDisponivel();
        VeiculoTransporteEscolar::query()->whereKey($id)->delete();
    }

    public function removerAluno(int $id): void
    {
        $this->assertSchemaDisponivel();
        AlunoTransporteEscolar::query()->whereKey($id)->delete();
    }

    public function removerVinculo(int $id): void
    {
        $this->assertSchemaDisponivel();
        LinhaVeiculoTransporteEscolar::query()->whereKey($id)->delete();
    }

    public function removerPonto(int $id): void
    {
        $this->assertSchemaDisponivel();
        $this->assertPontosDisponiveis();
        PontoTransporteEscolar::query()->whereKey($id)->delete();
    }

    public function obterLinha(int $id): LinhaTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        return LinhaTransporteEscolar::query()->findOrFail($id);
    }

    public function obterVeiculo(int $id): VeiculoTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        return VeiculoTransporteEscolar::query()->findOrFail($id);
    }

    public function obterAluno(int $id): AlunoTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        return AlunoTransporteEscolar::query()->findOrFail($id);
    }

    public function obterVinculo(int $id): LinhaVeiculoTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        return LinhaVeiculoTransporteEscolar::query()->findOrFail($id);
    }

    public function obterPonto(int $id): PontoTransporteEscolar
    {
        $this->assertSchemaDisponivel();
        $this->assertPontosDisponiveis();
        return PontoTransporteEscolar::query()->findOrFail($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function resumoLinha(LinhaTransporteEscolar $linha): array
    {
        return $this->resumirLinhaModel($linha);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function linhas(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_linhas')) {
            return [];
        }

        return LinhaTransporteEscolar::query()
            ->with('pontos')
            ->orderBy('codigo')
            ->get()
            ->map(function (LinhaTransporteEscolar $linha): array {
                return array_merge([
                    'id' => $linha->id,
                    'codigo' => $linha->codigo,
                    'nome' => $linha->nome,
                    'tipo_servico' => $linha->tipo_servico,
                    'horario_saida' => $linha->horario_saida,
                    'horario_retorno' => $linha->horario_retorno,
                    'custo_mensal' => $linha->custo_mensal,
                    'unidade_escolar' => $linha->unidade_escolar,
                    'rota_descricao' => $linha->rota_descricao,
                    'ativo' => (bool) $linha->ativo,
                ], $this->resumirLinhaModel($linha));
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function veiculos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_veiculos')) {
            return [];
        }

        return VeiculoTransporteEscolar::query()->orderBy('placa')->get()->map(static function (VeiculoTransporteEscolar $veiculo): array {
            return [
                'id' => $veiculo->id,
                'placa' => $veiculo->placa,
                'modelo' => $veiculo->modelo,
                'motorista_nome' => $veiculo->motorista_nome,
                'capacidade' => $veiculo->capacidade,
                'situacao' => $veiculo->situacao,
                'observacao' => $veiculo->observacao,
            ];
        })->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function pontos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_pontos')) {
            return [];
        }

        return PontoTransporteEscolar::query()
            ->with('linha')
            ->orderBy('linha_id')
            ->orderBy('ordem')
            ->get()
            ->map(static function (PontoTransporteEscolar $ponto): array {
                return [
                    'id' => $ponto->id,
                    'linha_id' => $ponto->linha_id,
                    'linha_codigo' => $ponto->linha ? $ponto->linha->codigo : null,
                    'nome' => $ponto->nome,
                    'endereco' => $ponto->endereco,
                    'tipo_ponto' => $ponto->tipo_ponto,
                    'ordem' => $ponto->ordem,
                    'observacao' => $ponto->observacao,
                    'ativo' => (bool) $ponto->ativo,
                ];
            })
            ->all();
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
    private function resumirLinhaModel(LinhaTransporteEscolar $linha): array
    {
        $pontos = $this->pontosDaLinha($linha);
        $roteiro = $this->roteiroDaLinha($pontos);
        $resumido = trim($roteiro['resumido']);

        if ($resumido === '') {
            $resumido = trim((string) $linha->rota_descricao);
        }

        if ($resumido === '') {
            $resumido = 'Roteiro sem pontos cadastrados';
        }

        return [
            'pontos_total' => count($pontos),
            'roteiro_resumido' => $resumido,
            'roteiro_detalhado' => $roteiro['detalhado'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vinculos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_linha_veiculos')) {
            return [];
        }

        return LinhaVeiculoTransporteEscolar::query()
            ->with(['linha', 'veiculo'])
            ->orderByDesc('id')
            ->get()
            ->map(static function (LinhaVeiculoTransporteEscolar $vinculo): array {
                return [
                    'id' => $vinculo->id,
                    'linha_id' => $vinculo->linha_id,
                    'veiculo_id' => $vinculo->veiculo_id,
                    'linha_codigo' => $vinculo->linha ? $vinculo->linha->codigo : null,
                    'veiculo_placa' => $vinculo->veiculo ? $vinculo->veiculo->placa : null,
                    'data_inicio' => $vinculo->data_inicio ? $vinculo->data_inicio->format('Y-m-d') : null,
                    'data_fim' => $vinculo->data_fim ? $vinculo->data_fim->format('Y-m-d') : null,
                    'observacao' => $vinculo->observacao,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function alunos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_alunos')) {
            return [];
        }

        return AlunoTransporteEscolar::query()->with('linha')->orderBy('aluno_nome')->get()->map(static function (AlunoTransporteEscolar $aluno): array {
            return [
                'id' => $aluno->id,
                'linha_id' => $aluno->linha_id,
                'aluno_nome' => $aluno->aluno_nome,
                'aluno_cpf' => $aluno->aluno_cpf,
                'escola_nome' => $aluno->escola_nome,
                'local_embarque' => $aluno->local_embarque,
                'motivo_uso' => $aluno->motivo_uso,
                'periodo_uso' => $aluno->periodo_uso,
                'situacao_matricula' => $aluno->situacao_matricula,
                'foto_path' => $aluno->foto_path,
                'utiliza_transporte' => (bool) $aluno->utiliza_transporte,
                'linha_codigo' => $aluno->linha ? $aluno->linha->codigo : null,
            ];
        })->all();
    }

    public function fotoDataUri(?string $fotoPath): ?string
    {
        if ($fotoPath === null || $fotoPath === '') {
            return null;
        }

        if (!Storage::disk('public')->exists($fotoPath)) {
            return null;
        }

        $conteudo = Storage::disk('public')->get($fotoPath);
        $mime = Storage::disk('public')->mimeType($fotoPath) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($conteudo);
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function assertSchemaDisponivel(): void
    {
        if (!$this->hasTable('educacao_transporte_escolar_linhas')) {
            throw new InvalidArgumentException('Base de transporte escolar nao disponivel. Execute a migration.');
        }
    }

    private function assertPontosDisponiveis(): void
    {
        if (!$this->hasTable('educacao_transporte_escolar_pontos')) {
            throw new InvalidArgumentException('Base de pontos do transporte escolar nao disponivel. Execute a migration.');
        }
    }
}
