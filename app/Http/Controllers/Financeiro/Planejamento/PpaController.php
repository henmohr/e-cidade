<?php

namespace App\Http\Controllers\Financeiro\Planejamento;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financeiro\Planejamento\ListPpaOrcamentoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaConfrontoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaConsolidacaoEntidadesRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaRelatorioGerencialRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaRelatorioObrigatorioRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaCompatibilizacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaAvaliacaoResultadosRequest;
use App\Http\Requests\Financeiro\Planejamento\ListPpaIndicadoresAplicacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAlteracaoReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAudienciaPublicaAnexoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaAudienciaPublicaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaMetaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaPlanoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaProgramaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaRateioReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaImportacaoRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaImportacaoLoaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaReceitaRequest;
use App\Http\Requests\Financeiro\Planejamento\StorePpaTransferenciaFinanceiraRequest;
use App\Services\Financeiro\Planejamento\Ppa\PpaAlteracaoReceitaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaAudienciaPublicaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaCadastroService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConfrontoReceitaDespesaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConsolidacaoEntidadesService;
use App\Services\Financeiro\Planejamento\Ppa\PpaCompatibilizacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaAvaliacaoResultadosService;
use App\Services\Financeiro\Planejamento\Ppa\PpaIndicadoresAplicacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaConsultaOrcamentoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaImportacaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaImportacaoLoaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaProjecaoService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRateioReceitaService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRelatorioGerencialService;
use App\Services\Financeiro\Planejamento\Ppa\PpaRelatoriosObrigatoriosService;
use App\Services\Financeiro\Planejamento\Ppa\PpaTransferenciaFinanceiraService;
use App\Services\Financeiro\Planejamento\Ppa\PpaVersaoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PpaController extends Controller
{
    public function storePlano(StorePpaPlanoRequest $request, PpaCadastroService $service): JsonResponse
    {
        try {
            $resultado = $service->criarPlano($request->validated(), true);
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeVersao(int $planoId, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->criarNovaVersao($planoId, 'Nova versao via API');
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storePrograma(int $versaoId, StorePpaProgramaRequest $request, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->adicionarPrograma($versaoId, $request->validated());
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeReceita(int $versaoId, StorePpaReceitaRequest $request, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->adicionarReceita($versaoId, $request->validated());
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function ratearReceita(
        int $versaoId,
        StorePpaRateioReceitaRequest $request,
        PpaRateioReceitaService $service
    ): JsonResponse {
        try {
            $resultado = $service->ratear($versaoId, $request->validated());
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeMeta(int $versaoId, StorePpaMetaRequest $request, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->adicionarMeta($versaoId, $request->validated());
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function publicarVersao(int $versaoId, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->publicarVersao($versaoId);
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function showVersao(int $versaoId, PpaVersaoService $service): JsonResponse
    {
        try {
            $resultado = $service->consultarVersao($versaoId);
            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }

    public function importarVersao(
        int $versaoId,
        StorePpaImportacaoRequest $request,
        PpaImportacaoService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->importarVersao(
                (int) $dados['versao_origem_id'],
                $versaoId,
                $dados['opcoes'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function importarLoa(
        int $versaoId,
        StorePpaImportacaoLoaRequest $request,
        PpaImportacaoLoaService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->importar(
                $versaoId,
                (int) $dados['exercicio_loa'],
                $dados
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function projetarVersao(int $versaoId, PpaProjecaoService $service): JsonResponse
    {
        try {
            $resultado = $service->projetarPorVersao($versaoId);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeAlteracaoReceita(
        int $versaoId,
        StorePpaAlteracaoReceitaRequest $request,
        PpaAlteracaoReceitaService $service
    ): JsonResponse {
        try {
            $resultado = $service->registrarAlteracao($versaoId, $request->validated());

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function listAlteracoesReceita(
        int $versaoId,
        Request $request,
        PpaAlteracaoReceitaService $service
    ): JsonResponse {
        try {
            $contaReceita = $request->query('conta_receita');
            $contaReceita = is_string($contaReceita) ? $contaReceita : null;
            $resultado = $service->consultarAlteracoes($versaoId, $contaReceita);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }

    public function consultarOrcamento(
        int $versaoId,
        ListPpaOrcamentoRequest $request,
        PpaConsultaOrcamentoService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->consultarPorVersao($versaoId, $dados['ate_data'] ?? null);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeTransferenciaFinanceira(
        int $versaoId,
        StorePpaTransferenciaFinanceiraRequest $request,
        PpaTransferenciaFinanceiraService $service
    ): JsonResponse {
        try {
            $resultado = $service->cadastrar($versaoId, $request->validated());

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function listTransferenciasFinanceiras(
        int $versaoId,
        PpaTransferenciaFinanceiraService $service
    ): JsonResponse {
        try {
            $resultado = $service->listar($versaoId);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }

    public function confrontarReceitaDespesa(
        ListPpaConfrontoRequest $request,
        PpaConfrontoReceitaDespesaService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->confrontar(
                $dados['versoes_ids'] ?? [],
                $dados['ate_data'] ?? null,
                $dados['entidades_ids'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function consolidarEntidades(
        ListPpaConsolidacaoEntidadesRequest $request,
        PpaConsolidacaoEntidadesService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->consolidar(
                $dados['versoes_ids'] ?? [],
                $dados['entidades_ids'] ?? [],
                $dados['ate_data'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function relatorioGerencial(
        ListPpaRelatorioGerencialRequest $request,
        PpaRelatorioGerencialService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->gerar(
                $dados['versoes_ids'] ?? [],
                $dados['entidades_ids'] ?? null,
                $dados['ate_data'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function relatorioObrigatorio(
        ListPpaRelatorioObrigatorioRequest $request,
        PpaRelatoriosObrigatoriosService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->gerar(
                (string) ($dados['tipo'] ?? ''),
                $dados['versoes_ids'] ?? [],
                $dados['entidades_ids'] ?? null,
                $dados['ate_data'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function compatibilizacao(
        ListPpaCompatibilizacaoRequest $request,
        PpaCompatibilizacaoService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->gerar(
                (int) ($dados['versao_ppa_id'] ?? 0),
                (int) ($dados['exercicio_ldo'] ?? 0),
                (int) ($dados['exercicio_loa'] ?? 0),
                isset($dados['instituicao_id']) ? (int) $dados['instituicao_id'] : null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function avaliacaoResultados(
        ListPpaAvaliacaoResultadosRequest $request,
        PpaAvaliacaoResultadosService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->gerar(
                (int) ($dados['versao_id'] ?? 0),
                $dados['exercicios'] ?? null,
                $dados['entidades_ids'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function indicadoresAplicacao(
        ListPpaIndicadoresAplicacaoRequest $request,
        PpaIndicadoresAplicacaoService $service
    ): JsonResponse {
        try {
            $dados = $request->validated();
            $resultado = $service->gerar(
                (int) ($dados['versao_id'] ?? 0),
                $dados['exercicios'] ?? null,
                $dados['entidades_ids'] ?? null
            );

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function storeAudienciaPublica(
        int $versaoId,
        StorePpaAudienciaPublicaRequest $request,
        PpaAudienciaPublicaService $service
    ): JsonResponse {
        try {
            $resultado = $service->registrarAudiencia($versaoId, $request->validated());

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function listAudienciasPublicas(
        int $versaoId,
        PpaAudienciaPublicaService $service
    ): JsonResponse {
        try {
            $resultado = $service->listarAudiencias($versaoId);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }

    public function storeAudienciaPublicaAnexo(
        int $audienciaId,
        StorePpaAudienciaPublicaAnexoRequest $request,
        PpaAudienciaPublicaService $service
    ): JsonResponse {
        try {
            $resultado = $service->anexarAta($audienciaId, $request->validated());

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 422);
        }
    }

    public function listAudienciaPublicaAnexos(
        int $audienciaId,
        PpaAudienciaPublicaService $service
    ): JsonResponse {
        try {
            $resultado = $service->listarAnexos($audienciaId);

            return new JsonResponse([
                'status' => 'ok',
                'timestamp' => date('c'),
                'data' => $resultado->toArray(),
            ]);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }

    public function downloadAudienciaPublicaAnexo(
        int $anexoId,
        PpaAudienciaPublicaService $service
    ) {
        try {
            $anexo = $service->obterAnexoParaDownload($anexoId);
            $disk = (string) ($anexo['storage_disk'] ?? 'local');
            $path = (string) ($anexo['storage_path'] ?? '');
            $nomeOriginal = (string) ($anexo['nome_original'] ?? ('anexo-' . $anexoId));
            $mimeType = $anexo['mime_type'] ?? null;

            $headers = [];
            if (is_string($mimeType) && $mimeType !== '') {
                $headers['Content-Type'] = $mimeType;
            }

            return Storage::disk($disk)->download($path, $nomeOriginal, $headers);
        } catch (InvalidArgumentException $e) {
            return new JsonResponse([
                'status' => 'erro',
                'timestamp' => date('c'),
                'mensagem' => $e->getMessage(),
            ], 404);
        }
    }
}
