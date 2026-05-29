<?php

namespace App\Http\Controllers\Educacao;

use App\Http\Controllers\Controller;
use App\Support\Educacao\TransporteEscolarDashboard;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarExportService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarSeteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransporteEscolarWebController extends Controller
{
    public function index(Request $request, TransporteEscolarDashboard $dashboard, TransporteEscolarExportService $service): View
    {
        $filtros = $this->filtros($request);
        $painel = $dashboard->payload();
        $base = $service->payload($filtros);

        $painel['linhas'] = $base['linhas'];
        $painel['pontos'] = $base['pontos'];
        $painel['veiculos'] = $base['veiculos'];
        $painel['alunos'] = $base['alunos'];
        $painel['filtros'] = $filtros;
        $painel['filtros_aplicados'] = $base['filtros_aplicados'];
        $painel['filtro_descricao'] = $base['filtro_descricao'];
        $painel['linhas_disponiveis'] = $base['linhas_disponiveis'];
        $painel['escolas_disponiveis'] = $base['escolas_disponiveis'];
        $painel['periodos_disponiveis'] = $base['periodos_disponiveis'];

        $painel['indicadores'] = $this->ajustarIndicadores($painel['indicadores'], $base);

        return view('educacao.transporte-escolar.index', $painel);
    }

    public function export(Request $request, TransporteEscolarExportService $service): JsonResponse
    {
        $filtros = $this->filtros($request);

        return response()->json([
            'status' => 'ok',
            'module' => 'transportescolar',
            'data' => $service->payload($filtros),
        ]);
    }

    public function exportCsv(Request $request, TransporteEscolarExportService $service): StreamedResponse
    {
        $nomeArquivo = 'transporte_escolar_' . date('Ymd_His') . '.csv';
        $conteudo = $service->csv($this->filtros($request));

        $response = new StreamedResponse(static function () use ($conteudo): void {
            echo $conteudo;
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');

        return $response;
    }

    public function exportSete(Request $request, TransporteEscolarSeteService $service): StreamedResponse
    {
        $nomeArquivo = 'transporte_escolar_sete_' . date('Ymd_His') . '.json';
        $conteudo = $service->exportarJson($this->filtros($request));

        $response = new StreamedResponse(static function () use ($conteudo): void {
            echo $conteudo;
        });

        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');

        return $response;
    }

    public function importSete(Request $request, TransporteEscolarSeteService $service): RedirectResponse
    {
        $request->validate([
            'arquivo_sete' => ['required', 'file', 'max:5120'],
        ]);

        $arquivo = $request->file('arquivo_sete');
        if ($arquivo === null) {
            return redirect()->route('transportescolar.web.index')->with('error', 'Arquivo SETE nao informado.');
        }

        try {
            $resumo = $service->importarJson((string) file_get_contents($arquivo->getRealPath()));

            return redirect()
                ->route('transportescolar.web.index')
                ->with('status', 'Importacao SETE executada com sucesso. Linhas: ' . ($resumo['linhas_criadas'] + $resumo['linhas_atualizadas']) . ', veiculos: ' . ($resumo['veiculos_criados'] + $resumo['veiculos_atualizados']) . ', alunos: ' . ($resumo['alunos_criados'] + $resumo['alunos_atualizados']) . '.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('transportescolar.web.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * @return array<string, string>
     */
    private function filtros(Request $request): array
    {
        return [
            'linha' => trim((string) $request->query('linha', '')),
            'periodo' => trim((string) $request->query('periodo', '')),
            'escola' => trim((string) $request->query('escola', '')),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $indicadores
     * @param array<string, mixed> $base
     * @return array<int, array<string, mixed>>
     */
    private function ajustarIndicadores(array $indicadores, array $base): array
    {
        if (isset($indicadores[0], $base['linhas'])) {
            $indicadores[0]['valor'] = (string) count($base['linhas']);
        }

        if (isset($indicadores[1], $base['veiculos'])) {
            $indicadores[1]['valor'] = (string) count($base['veiculos']);
        }

        if (isset($indicadores[2], $base['alunos'])) {
            $indicadores[2]['valor'] = (string) count($base['alunos']);
        }

        return $indicadores;
    }
}
