<?php

namespace App\Services\Financeiro\Licitacao;

use App\Services\Financeiro\Integracao\HomologacaoAnexosService;
use RuntimeException;

class PacoteFinalBancaService
{
    private CoberturaLicitacaoService $coberturaService;
    private HomologacaoAnexosService $anexosService;

    public function __construct(
        ?CoberturaLicitacaoService $coberturaService = null,
        ?HomologacaoAnexosService $anexosService = null
    ) {
        $this->coberturaService = $coberturaService ?? new CoberturaLicitacaoService();
        $this->anexosService = $anexosService ?? new HomologacaoAnexosService();
    }

    /**
     * @param array<int, string>|null $documentosObrigatorios
     * @return array<string, mixed>
     */
    public function gerar(
        string $arquivoMatriz,
        string $diretorioAnexos,
        string $diretorioSaida,
        ?array $documentosObrigatorios = null
    ): array {
        $docsObrigatorios = $documentosObrigatorios ?: $this->documentosObrigatoriosPadrao();

        if (!is_dir($diretorioSaida) && !mkdir($diretorioSaida, 0775, true) && !is_dir($diretorioSaida)) {
            throw new RuntimeException('Nao foi possivel criar diretorio de saida do pacote final.');
        }

        $cobertura = $this->coberturaService->gerarResumo($arquivoMatriz);
        $anexos = $this->anexosService->validarDiretorio($diretorioAnexos);
        $validacaoDocumentos = $this->validarDocumentosObrigatorios($docsObrigatorios);

        $statusFinal = (
            ($cobertura['status_recomendado'] ?? 'apto_com_ressalvas') === 'apto_para_banca'
            && ($anexos['status'] ?? 'pendente') === 'ok'
            && count($validacaoDocumentos['ausentes']) === 0
        ) ? 'apto_para_banca' : 'apto_com_ressalvas';

        $manifesto = [
            'gerado_em' => date('c'),
            'status_final' => $statusFinal,
            'cobertura_licitacao' => [
                'arquivo_matriz' => $arquivoMatriz,
                'total_itens' => (int) ($cobertura['total_itens'] ?? 0),
                'percentual_atingido' => (float) ($cobertura['percentual_atingido'] ?? 0),
                'status_recomendado' => (string) ($cobertura['status_recomendado'] ?? 'apto_com_ressalvas'),
            ],
            'anexos_homologacao' => [
                'diretorio' => $diretorioAnexos,
                'status' => (string) ($anexos['status'] ?? 'pendente'),
                'ausentes' => $anexos['ausentes'] ?? [],
                'vazios' => $anexos['vazios'] ?? [],
            ],
            'documentos_obrigatorios' => $validacaoDocumentos,
        ];

        $manifestoPath = rtrim($diretorioSaida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'manifesto_final_banca.json';
        $resumoPath = rtrim($diretorioSaida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'resumo_final_banca.md';

        file_put_contents($manifestoPath, (string) json_encode($manifesto, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($resumoPath, $this->gerarResumoMarkdown($manifesto));

        return [
            'status_final' => $statusFinal,
            'manifesto' => $manifestoPath,
            'resumo' => $resumoPath,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function documentosObrigatoriosPadrao(): array
    {
        return [
            'docs/matriz_gap_poc_100.md',
            'docs/plano_execucao_sprints_poc.md',
            'docs/roteiro_oficial_demonstracao_poc.md',
            'docs/simulacao_integral_poc.md',
            'docs/sprint9_relatorio_cobertura_licitacao.md',
            'docs/pacote_documental_poc.md',
        ];
    }

    /**
     * @param array<int, string> $documentos
     * @return array<string, mixed>
     */
    private function validarDocumentosObrigatorios(array $documentos): array
    {
        $presentes = [];
        $ausentes = [];

        foreach ($documentos as $doc) {
            if (is_file($doc)) {
                $presentes[] = $doc;
                continue;
            }

            $ausentes[] = $doc;
        }

        return [
            'total' => count($documentos),
            'presentes' => $presentes,
            'ausentes' => $ausentes,
        ];
    }

    /**
     * @param array<string, mixed> $manifesto
     */
    private function gerarResumoMarkdown(array $manifesto): string
    {
        $docs = $manifesto['documentos_obrigatorios'] ?? [];
        $anexos = $manifesto['anexos_homologacao'] ?? [];
        $cobertura = $manifesto['cobertura_licitacao'] ?? [];

        $linhas = [
            '# Resumo Final de Banca',
            '',
            '- Gerado em: ' . (string) ($manifesto['gerado_em'] ?? ''),
            '- Status final: ' . (string) ($manifesto['status_final'] ?? 'apto_com_ressalvas'),
            '',
            '## Cobertura da licitacao',
            '- Percentual atingido: ' . number_format((float) ($cobertura['percentual_atingido'] ?? 0), 2, '.', '') . '%',
            '- Status cobertura: ' . (string) ($cobertura['status_recomendado'] ?? 'apto_com_ressalvas'),
            '',
            '## Anexos de homologacao',
            '- Status: ' . (string) ($anexos['status'] ?? 'pendente'),
            '- Ausentes: ' . count($anexos['ausentes'] ?? []),
            '- Vazios: ' . count($anexos['vazios'] ?? []),
            '',
            '## Documentos obrigatorios',
            '- Total: ' . (int) ($docs['total'] ?? 0),
            '- Presentes: ' . count($docs['presentes'] ?? []),
            '- Ausentes: ' . count($docs['ausentes'] ?? []),
        ];

        return implode("\n", $linhas) . "\n";
    }
}
