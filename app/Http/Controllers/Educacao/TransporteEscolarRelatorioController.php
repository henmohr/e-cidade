<?php

namespace App\Http\Controllers\Educacao;

use App\Http\Controllers\Controller;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarRelatorioService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransporteEscolarRelatorioController extends Controller
{
    public function index(Request $request, TransporteEscolarRelatorioService $service): View
    {
        $filtros = $this->filtros($request);

        return view('educacao.transporte-escolar.relatorios', $service->payload($filtros));
    }

    public function pdf(Request $request, TransporteEscolarRelatorioService $service)
    {
        $dados = $service->payload($this->filtros($request));
        $html = view('educacao.transporte-escolar.relatorios-pdf', $dados)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);
        $mpdf->SetTitle('Relatorios legais do Transporte Escolar');
        $mpdf->WriteHTML($html);

        $conteudo = $mpdf->Output('', Destination::STRING_RETURN);
        $arquivo = 'relatorios-legais-transporte-escolar.pdf';

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $arquivo . '"',
        ]);
    }

    public function csv(Request $request, TransporteEscolarRelatorioService $service): StreamedResponse
    {
        $nomeArquivo = 'relatorios-legais-transporte-escolar_' . date('Ymd_His') . '.csv';
        $conteudo = $service->csv($this->filtros($request));

        $response = new StreamedResponse(static function () use ($conteudo): void {
            echo $conteudo;
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $nomeArquivo . '"');

        return $response;
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
}
