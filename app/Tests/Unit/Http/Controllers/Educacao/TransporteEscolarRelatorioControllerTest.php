<?php

namespace App\Tests\Unit\Http\Controllers\Educacao;

use App\Http\Controllers\Educacao\TransporteEscolarRelatorioController;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarRelatorioService;
use App\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransporteEscolarRelatorioControllerTest extends TestCase
{
    public function testIndexRetornaViewDeRelatorios(): void
    {
        $service = $this->createMock(TransporteEscolarRelatorioService::class);
        $service->expects($this->once())
            ->method('payload')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn($this->payload());

        $controller = new TransporteEscolarRelatorioController();
        $response = $controller->index(Request::create('/web/transporte-escolar/relatorios', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $service);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('educacao.transporte-escolar.relatorios', $response->name());
    }

    public function testCsvRetornaArquivoStreaming(): void
    {
        $service = $this->createMock(TransporteEscolarRelatorioService::class);
        $service->expects($this->once())
            ->method('csv')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn("secao;codigo;titulo;status;descricao\nlegal;A7.1;Roteiros e horarios;disponivel;ok\n");

        $controller = new TransporteEscolarRelatorioController();
        $response = $controller->csv(Request::create('/web/transporte-escolar/relatorios.csv', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $service);

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    public function testPdfRetornaArquivoPdf(): void
    {
        $service = $this->createMock(TransporteEscolarRelatorioService::class);
        $service->expects($this->once())
            ->method('payload')
            ->with([
                'linha' => 'TRE-01',
                'periodo' => 'Manha',
                'escola' => 'EMEF Centro',
            ])
            ->willReturn($this->payload());

        $controller = new TransporteEscolarRelatorioController();
        $response = $controller->pdf(Request::create('/web/transporte-escolar/relatorios.pdf', 'GET', [
            'linha' => 'TRE-01',
            'periodo' => 'Manha',
            'escola' => 'EMEF Centro',
        ]), $service);

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('relatorios-legais-transporte-escolar.pdf', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'titulo' => 'Relatorios legais do Transporte Escolar',
            'subtitulo' => 'Atendimento aos relatorios obrigatorios e controle da area',
            'status' => 'em_implantacao',
            'gerado_em' => '29/05/2026 15:00',
            'filtros' => [
                'linha' => '',
                'periodo' => '',
                'escola' => '',
            ],
            'filtros_aplicados' => [],
            'filtro_descricao' => 'Sem filtros aplicados',
            'linhas_disponiveis' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro'],
            ],
            'linha_selecionada' => [],
            'periodos_disponiveis' => ['Manha'],
            'escolas_disponiveis' => ['EMEF Centro'],
            'resumo' => [
                ['titulo' => 'Linhas em base', 'valor' => '1', 'detalhe' => 'ok'],
                ['titulo' => 'Veiculos monitorados', 'valor' => '1', 'detalhe' => 'ok'],
                ['titulo' => 'Alunos vinculados', 'valor' => '1', 'detalhe' => 'ok'],
                ['titulo' => 'Pendencias documentais', 'valor' => '2', 'detalhe' => 'ok'],
            ],
            'checklist_legal' => [
                ['codigo' => 'A7.1', 'titulo' => 'Roteiros e horarios', 'status' => 'disponivel', 'evidencia' => 'ok'],
            ],
            'relatorios_obrigatorios' => [
                ['nome' => 'Roteiro por linha', 'finalidade' => 'ok', 'status' => 'disponivel'],
            ],
            'linhas' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo' => 'proprio', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 1,00'],
            ],
            'veiculos' => [
                ['placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista' => 'Joao', 'status' => 'disponivel'],
            ],
            'alunos' => [
                ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEF Centro', 'linha' => 'TRE-01', 'embarque' => 'Rua A', 'periodo_uso' => 'Manha'],
            ],
            'status_veiculos' => ['disponivel' => 1],
            'status_linhas' => ['proprio' => 1],
            'integracoes' => ['Cadastro escolar'],
            'pendencias' => ['Fechar layout'],
            'base_legal' => [
                ['norma' => 'Controle interno', 'referencia' => 'ok'],
            ],
        ];
    }
}
