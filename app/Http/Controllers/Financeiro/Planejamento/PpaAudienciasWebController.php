<?php

namespace App\Http\Controllers\Financeiro\Planejamento;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\Planejamento\PpaAudienciaPublica;
use App\Models\Financeiro\Planejamento\PpaVersao;
use App\Services\Financeiro\Planejamento\Ppa\PpaAudienciaPublicaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use InvalidArgumentException;

class PpaAudienciasWebController extends Controller
{
    public function index(Request $request): View
    {
        $versoes = PpaVersao::query()
            ->with('plano')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $versaoId = (int) $request->query('versao_id', 0);
        if ($versaoId <= 0 && $versoes->count() > 0) {
            $versaoId = (int) $versoes->first()->id;
        }

        $audiencias = collect();
        if ($versaoId > 0) {
            $audiencias = PpaAudienciaPublica::query()
                ->with('anexos')
                ->where('ppa_versao_id', $versaoId)
                ->orderByDesc('data_realizacao')
                ->orderByDesc('id')
                ->get();
        }

        return view('financeiro.planejamento.ppa.audiencias', [
            'versoes' => $versoes,
            'versaoIdSelecionada' => $versaoId,
            'audiencias' => $audiencias,
        ]);
    }

    public function store(Request $request, PpaAudienciaPublicaService $service): RedirectResponse
    {
        $dados = $request->validate([
            'versao_id' => ['required', 'integer', 'min:1'],
            'data_realizacao' => ['required', 'date_format:Y-m-d'],
            'solicitacoes_comunidade' => ['required', 'string'],
            'bairro_atendido' => ['required', 'string', 'max:120'],
            'contato_solicitante' => ['required', 'string', 'max:255'],
            'orgao_responsavel' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:recebida,em_analise,deferida,indeferida,concluida'],
            'observacao' => ['nullable', 'string'],
        ]);

        $versaoId = (int) $dados['versao_id'];
        unset($dados['versao_id']);

        try {
            $service->registrarAudiencia($versaoId, $dados);
            return redirect()
                ->route('planejamento.ppa.audiencias.index', ['versao_id' => $versaoId])
                ->with('success', 'Audiencia publica cadastrada com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('planejamento.ppa.audiencias.index', ['versao_id' => $versaoId])
                ->with('error', $e->getMessage());
        }
    }

    public function storeAnexo(
        int $audienciaId,
        Request $request,
        PpaAudienciaPublicaService $service
    ): RedirectResponse {
        $dados = $request->validate([
            'versao_id' => ['required', 'integer', 'min:1'],
            'ata' => ['required', 'file', 'max:8192'],
        ]);

        $versaoId = (int) $dados['versao_id'];

        $arquivo = $request->file('ata');
        if ($arquivo === null) {
            return redirect()
                ->route('planejamento.ppa.audiencias.index', ['versao_id' => $versaoId])
                ->with('error', 'Arquivo da ata nao informado.');
        }

        $conteudo = base64_encode((string) file_get_contents($arquivo->getRealPath()));

        try {
            $service->anexarAta($audienciaId, [
                'nome_arquivo' => (string) $arquivo->getClientOriginalName(),
                'conteudo_base64' => $conteudo,
                'mime_type' => $arquivo->getMimeType(),
            ]);

            return redirect()
                ->route('planejamento.ppa.audiencias.index', ['versao_id' => $versaoId])
                ->with('success', 'Ata anexada com sucesso.');
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('planejamento.ppa.audiencias.index', ['versao_id' => $versaoId])
                ->with('error', $e->getMessage());
        }
    }

    public function downloadAnexo(int $anexoId, PpaAudienciaPublicaService $service)
    {
        try {
            $anexo = $service->obterAnexoParaDownload($anexoId);
            $disk = (string) ($anexo['storage_disk'] ?? 'local');
            $path = (string) ($anexo['storage_path'] ?? '');
            $nome = (string) ($anexo['nome_original'] ?? ('anexo-' . $anexoId));
            $mime = $anexo['mime_type'] ?? null;

            $headers = [];
            if (is_string($mime) && $mime !== '') {
                $headers['Content-Type'] = $mime;
            }

            return Storage::disk($disk)->download($path, $nome, $headers);
        } catch (InvalidArgumentException $e) {
            return redirect()
                ->route('planejamento.ppa.audiencias.index')
                ->with('error', $e->getMessage());
        }
    }
}
