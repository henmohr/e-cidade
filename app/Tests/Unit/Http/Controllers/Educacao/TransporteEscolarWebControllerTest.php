<?php

namespace App\Tests\Unit\Http\Controllers\Educacao;

use App\Http\Controllers\Educacao\TransporteEscolarWebController;
use App\Support\Educacao\TransporteEscolarDashboard;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarExportService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarSeteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransporteEscolarWebControllerTest extends TestCase
{
    public function testIndexRetornaViewDoModulo(): void
    {
        $dashboard = $this->createMock(TransporteEscolarDashboard::class);
        $dashboard->method('payload')->willReturn([
            'indicadores' => [],
            'requisitos' => [],
            'linhas' => [],
            'veiculos' => [],
            'relatorios' => [],
            'integracoes' => [],
            'legado' => [],
            'acoes' => [],
        ]);

        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->expects($this->once())
            ->method('payload')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn([
                'linhas' => [],
                'pontos' => [],
                'veiculos' => [],
                'alunos' => [],
                'filtros_aplicados' => [],
                'filtro_descricao' => 'Sem filtros aplicados',
                'linhas_disponiveis' => [],
                'escolas_disponiveis' => [],
                'periodos_disponiveis' => [],
            ]);

        $controller = new TransporteEscolarWebController();
        $view = $controller->index(Request::create('/web/transporte-escolar', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $dashboard, $export);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('educacao.transporte-escolar.index', $view->name());
    }

    public function testExportCsvRetornaArquivoStreaming(): void
    {
        $service = $this->createMock(TransporteEscolarExportService::class);
        $service->expects($this->once())
            ->method('csv')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn("secao;codigo;nome;tipo;extra\nlinha;TRE-01;Linha;proprio;06:40 / 11:30\n");

        $controller = new TransporteEscolarWebController();
        $response = $controller->exportCsv(Request::create('/web/transporte-escolar/export.csv', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $service);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testExportSeteRetornaArquivoJson(): void
    {
        $service = $this->createMock(TransporteEscolarSeteService::class);
        $service->expects($this->once())
            ->method('exportarJson')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn('{"linhas":[],"veiculos":[],"vinculos":[],"alunos":[]}');

        $controller = new TransporteEscolarWebController();
        $response = $controller->exportSete(Request::create('/web/transporte-escolar/export/sete.json', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $service);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('application/json; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testImportSeteRedirecionaComStatus(): void
    {
        $arquivo = UploadedFile::fake()->createWithContent('sete.json', '{"linhas":[],"veiculos":[],"vinculos":[],"alunos":[]}');

        $service = $this->createMock(TransporteEscolarSeteService::class);
        $service->expects($this->once())
            ->method('importarJson')
            ->with('{"linhas":[],"veiculos":[],"vinculos":[],"alunos":[]}')
            ->willReturn([
                'linhas_criadas' => 0,
                'linhas_atualizadas' => 0,
                'veiculos_criados' => 0,
                'veiculos_atualizados' => 0,
                'vinculos_criados' => 0,
                'vinculos_atualizados' => 0,
                'alunos_criados' => 0,
                'alunos_atualizados' => 0,
            ]);

        $controller = new TransporteEscolarWebController();
        $response = $controller->importSete(Request::create('/web/transporte-escolar/import/sete', 'POST', [], [], [
            'arquivo_sete' => $arquivo,
        ]), $service);

        $this->assertSame(route('transportescolar.web.index'), $response->getTargetUrl());
    }
}
