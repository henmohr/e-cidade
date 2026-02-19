<?php

namespace App\Services\Financeiro\Planejamento\Ppa;

use App\Repositories\Financeiro\Planejamento\Ppa\PpaAudienciaRepository;
use App\Repositories\Financeiro\Planejamento\Ppa\PpaAudienciaRepositoryInterface;
use App\Repositories\Financeiro\Planejamento\Ppa\PpaRepository;
use App\Repositories\Financeiro\Planejamento\Ppa\PpaRepositoryInterface;
use App\Services\Financeiro\Planejamento\Ppa\Dto\PpaAudienciaPublicaResultado;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PpaAudienciaPublicaService
{
    private PpaRepositoryInterface $ppaRepository;
    private PpaAudienciaRepositoryInterface $audienciaRepository;
    private PpaValidacaoService $validacaoService;

    public function __construct(
        ?PpaRepositoryInterface $ppaRepository = null,
        ?PpaAudienciaRepositoryInterface $audienciaRepository = null,
        ?PpaValidacaoService $validacaoService = null
    ) {
        $this->ppaRepository = $ppaRepository ?? new PpaRepository();
        $this->audienciaRepository = $audienciaRepository ?? new PpaAudienciaRepository();
        $this->validacaoService = $validacaoService ?? new PpaValidacaoService();
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function registrarAudiencia(int $versaoId, array $dados, ?int $usuarioId = null): PpaAudienciaPublicaResultado
    {
        $versao = $this->obterVersaoEditavel($versaoId);

        $audiencia = $this->audienciaRepository->criarAudiencia([
            'ppa_versao_id' => $versaoId,
            'entidade_id' => $dados['entidade_id'] ?? null,
            'data_realizacao' => (string) ($dados['data_realizacao'] ?? ''),
            'solicitacoes_comunidade' => (string) ($dados['solicitacoes_comunidade'] ?? ''),
            'bairro_atendido' => (string) ($dados['bairro_atendido'] ?? ''),
            'contato_solicitante' => (string) ($dados['contato_solicitante'] ?? ''),
            'orgao_responsavel' => (string) ($dados['orgao_responsavel'] ?? ''),
            'status' => (string) ($dados['status'] ?? 'recebida'),
            'observacao' => $dados['observacao'] ?? null,
            'created_by' => $usuarioId,
            'updated_by' => $usuarioId,
        ]);

        return new PpaAudienciaPublicaResultado(
            (int) $versao['id'],
            'Audiencia publica registrada com sucesso.',
            ['audiencia' => $this->normalizarAudiencia($audiencia)]
        );
    }

    public function listarAudiencias(int $versaoId): PpaAudienciaPublicaResultado
    {
        $versao = $this->ppaRepository->obterVersaoPorId($versaoId);
        if ($versao === null) {
            throw new InvalidArgumentException('Versao PPA nao encontrada para consulta de audiencias.');
        }

        $audiencias = array_map(
            fn (array $audiencia): array => $this->normalizarAudiencia($audiencia),
            $this->audienciaRepository->listarAudienciasPorVersao($versaoId)
        );

        return new PpaAudienciaPublicaResultado(
            $versaoId,
            'Audiencias publicas consultadas com sucesso.',
            ['audiencias' => $audiencias],
            count($audiencias)
        );
    }

    /**
     * @param array<string, mixed> $dados
     */
    public function anexarAta(int $audienciaId, array $dados, ?int $usuarioId = null): PpaAudienciaPublicaResultado
    {
        $audiencia = $this->audienciaRepository->obterAudienciaPorId($audienciaId);
        if ($audiencia === null) {
            throw new InvalidArgumentException('Audiencia publica nao encontrada para anexo.');
        }

        $this->obterVersaoEditavel((int) $audiencia['ppa_versao_id']);

        $conteudo = $this->decodificarBase64((string) ($dados['conteudo_base64'] ?? ''));
        $nomeOriginal = trim((string) ($dados['nome_arquivo'] ?? ''));
        if ($nomeOriginal === '') {
            throw new InvalidArgumentException('Nome do arquivo do anexo e obrigatorio.');
        }

        $nomeArquivo = $this->gerarNomeArquivo($nomeOriginal);
        $caminho = sprintf('ppa/audiencias/%d/%s', $audienciaId, $nomeArquivo);
        $disk = 'local';

        $salvo = Storage::disk($disk)->put($caminho, $conteudo);
        if ($salvo !== true) {
            throw new InvalidArgumentException('Nao foi possivel salvar o arquivo do anexo da audiencia.');
        }

        $anexo = $this->audienciaRepository->criarAnexo([
            'ppa_audiencia_publica_id' => $audienciaId,
            'nome_original' => $nomeOriginal,
            'nome_arquivo' => $nomeArquivo,
            'mime_type' => $dados['mime_type'] ?? null,
            'tamanho_bytes' => strlen($conteudo),
            'storage_disk' => $disk,
            'storage_path' => $caminho,
            'hash_arquivo' => hash('sha256', $conteudo),
            'created_by' => $usuarioId,
            'updated_by' => $usuarioId,
        ]);

        return new PpaAudienciaPublicaResultado(
            (int) $audiencia['ppa_versao_id'],
            'Ata da audiencia anexada com sucesso.',
            ['anexo' => $this->normalizarAnexo($anexo)]
        );
    }

    public function listarAnexos(int $audienciaId): PpaAudienciaPublicaResultado
    {
        $audiencia = $this->audienciaRepository->obterAudienciaPorId($audienciaId);
        if ($audiencia === null) {
            throw new InvalidArgumentException('Audiencia publica nao encontrada para consulta de anexos.');
        }

        $anexos = array_map(
            fn (array $anexo): array => $this->normalizarAnexo($anexo),
            $this->audienciaRepository->listarAnexosPorAudiencia($audienciaId)
        );

        return new PpaAudienciaPublicaResultado(
            (int) $audiencia['ppa_versao_id'],
            'Anexos da audiencia consultados com sucesso.',
            ['audiencia_id' => $audienciaId, 'anexos' => $anexos],
            count($anexos)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function obterAnexoParaDownload(int $anexoId): array
    {
        $anexo = $this->audienciaRepository->obterAnexoPorId($anexoId);
        if ($anexo === null) {
            throw new InvalidArgumentException('Anexo de audiencia nao encontrado.');
        }

        $disk = (string) ($anexo['storage_disk'] ?? 'local');
        $path = (string) ($anexo['storage_path'] ?? '');
        if ($path === '' || !Storage::disk($disk)->exists($path)) {
            throw new InvalidArgumentException('Arquivo do anexo nao encontrado no armazenamento.');
        }

        return $anexo;
    }

    /**
     * @return array<string, mixed>
     */
    private function obterVersaoEditavel(int $versaoId): array
    {
        $versao = $this->ppaRepository->obterVersaoPorId($versaoId);
        if ($versao === null) {
            throw new InvalidArgumentException('Versao PPA nao encontrada para alteracao.');
        }

        $validacao = $this->validacaoService->validarCadastro([
            'operacao' => 'alteracao',
            'versao_status' => (string) ($versao['status'] ?? ''),
            'grau_plano_contas' => '1',
            'vinculos_tce' => [['codigo_vinculo' => 'validacao-interna']],
            'metas' => [],
        ]);

        if (!$validacao->valido) {
            throw new InvalidArgumentException('Nao e permitido alterar uma versao publicada.');
        }

        return $versao;
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizarAudiencia(array $audiencia): array
    {
        return [
            'id' => (int) ($audiencia['id'] ?? 0),
            'ppa_versao_id' => (int) ($audiencia['ppa_versao_id'] ?? 0),
            'entidade_id' => isset($audiencia['entidade_id']) ? (int) $audiencia['entidade_id'] : null,
            'data_realizacao' => (string) ($audiencia['data_realizacao'] ?? ''),
            'solicitacoes_comunidade' => (string) ($audiencia['solicitacoes_comunidade'] ?? ''),
            'bairro_atendido' => (string) ($audiencia['bairro_atendido'] ?? ''),
            'contato_solicitante' => (string) ($audiencia['contato_solicitante'] ?? ''),
            'orgao_responsavel' => (string) ($audiencia['orgao_responsavel'] ?? ''),
            'status' => (string) ($audiencia['status'] ?? ''),
            'observacao' => $audiencia['observacao'] ?? null,
            'created_at' => $audiencia['created_at'] ?? null,
            'updated_at' => $audiencia['updated_at'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizarAnexo(array $anexo): array
    {
        return [
            'id' => (int) ($anexo['id'] ?? 0),
            'ppa_audiencia_publica_id' => (int) ($anexo['ppa_audiencia_publica_id'] ?? 0),
            'nome_original' => (string) ($anexo['nome_original'] ?? ''),
            'mime_type' => $anexo['mime_type'] ?? null,
            'tamanho_bytes' => (int) ($anexo['tamanho_bytes'] ?? 0),
            'hash_arquivo' => $anexo['hash_arquivo'] ?? null,
            'download_url' => '/api/v1/financeiro/ppa/audiencias-publicas/anexos/' . (int) ($anexo['id'] ?? 0) . '/download',
            'created_at' => $anexo['created_at'] ?? null,
        ];
    }

    private function gerarNomeArquivo(string $nomeOriginal): string
    {
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $base = now()->format('YmdHis') . '_' . Str::lower(Str::random(12));

        if ($extensao === '') {
            return $base;
        }

        return $base . '.' . Str::lower($extensao);
    }

    private function decodificarBase64(string $base64): string
    {
        $valor = trim($base64);
        if ($valor === '') {
            throw new InvalidArgumentException('Conteudo do arquivo em base64 e obrigatorio.');
        }

        if (str_starts_with($valor, 'data:')) {
            $partes = explode(',', $valor, 2);
            $valor = $partes[1] ?? '';
        }

        $conteudo = base64_decode($valor, true);
        if ($conteudo === false || $conteudo === '') {
            throw new InvalidArgumentException('Conteudo base64 invalido para anexo da audiencia.');
        }

        return $conteudo;
    }
}
