<?php

namespace App\Tests\Unit\Services\Educacao;

use App\Services\Educacao\TransporteEscolar\TransporteEscolarExportService;
use App\Services\Educacao\TransporteEscolar\TransporteEscolarRelatorioService;
use App\Tests\TestCase;

class TransporteEscolarRelatorioServiceTest extends TestCase
{
    public function testPayloadContemChecklistLegalEResumo(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->method('payload')->willReturn([
            'linhas' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo' => 'proprio', 'unidade_escolar' => 'EMEF Centro', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 3.420,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Central -> Terminal'],
            ],
            'veiculos' => [
                ['placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista' => 'Joao', 'status' => 'disponivel'],
            ],
            'alunos' => [
                ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEF Centro', 'linha' => 'TRE-01', 'embarque' => 'Rua A', 'periodo_uso' => 'Manha'],
            ],
            'metadados' => [
                'gerado_em' => '2026-05-29T15:00:00-03:00',
                'fonte' => 'educacao_transporte_escolar',
                'versao' => 'v1',
            ],
        ]);

        $service = new TransporteEscolarRelatorioService($export);
        $payload = $service->payload();

        $this->assertSame('Relatorios legais do Transporte Escolar', $payload['titulo']);
        $this->assertNotEmpty($payload['checklist_legal']);
        $this->assertNotEmpty($payload['relatorios_obrigatorios']);
        $this->assertSame('em_implantacao', $payload['status']);
        $this->assertSame(1, $payload['status_veiculos']['disponivel']);
        $this->assertSame(['Manha'], $payload['periodos_disponiveis']);
        $this->assertSame(['EMEF Centro'], $payload['escolas_disponiveis']);
        $this->assertSame([['codigo' => 'TRE-01', 'nome' => 'Linha Centro']], $payload['linhas_disponiveis']);
        $this->assertSame('Sem filtros aplicados', $payload['filtro_descricao']);
    }

    public function testCsvContemSecaoLegalERelatorio(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->method('payload')->willReturn([
            'linhas' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo' => 'proprio', 'unidade_escolar' => 'EMEF Centro', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 3.420,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Central -> Terminal'],
            ],
            'veiculos' => [
                ['placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista' => 'Joao', 'status' => 'disponivel'],
            ],
            'alunos' => [
                ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEF Centro', 'linha' => 'TRE-01', 'embarque' => 'Rua A', 'periodo_uso' => 'Manha'],
            ],
            'metadados' => [
                'gerado_em' => '2026-05-29T15:00:00-03:00',
                'fonte' => 'educacao_transporte_escolar',
                'versao' => 'v1',
            ],
        ]);

        $service = new TransporteEscolarRelatorioService($export);
        $csv = $service->csv();

        $this->assertStringContainsString("secao;codigo;titulo;status;descricao", $csv);
        $this->assertStringContainsString('legal;A7.1', $csv);
        $this->assertStringContainsString('Roteiros e horarios', $csv);
        $this->assertStringContainsString('relatorio;;', $csv);
        $this->assertStringContainsString('Relatorio legal consolidado', $csv);
        $this->assertStringContainsString('linha;TRE-01', $csv);
        $this->assertStringContainsString('roteiro=Ponto Central -> Terminal', $csv);
    }

    public function testPayloadFiltraPorEscolaEPeriodo(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->method('payload')->willReturn([
            'linhas' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo' => 'proprio', 'unidade_escolar' => 'EMEF Centro', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 3.420,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Central -> Terminal'],
                ['codigo' => 'TRE-02', 'nome' => 'Linha Vila', 'tipo' => 'terceirizado', 'unidade_escolar' => 'EMEF Vila Nova', 'horario' => '05:55 / 12:10', 'custo' => 'R$ 8.150,00', 'pontos_total' => 1, 'roteiro_resumido' => 'Ponto Rural'],
            ],
            'veiculos' => [
                ['placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista' => 'Joao', 'status' => 'disponivel'],
            ],
            'alunos' => [
                ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEF Centro', 'linha' => 'TRE-01', 'embarque' => 'Rua A', 'periodo_uso' => 'Manha'],
                ['cpf' => '98765432100', 'nome' => 'Lucas Pereira', 'escola' => 'EMEF Vila Nova', 'linha' => 'TRE-02', 'embarque' => 'Rua B', 'periodo_uso' => 'Tarde'],
            ],
            'metadados' => [
                'gerado_em' => '2026-05-29T15:00:00-03:00',
                'fonte' => 'educacao_transporte_escolar',
                'versao' => 'v1',
            ],
        ]);

        $service = new TransporteEscolarRelatorioService($export);
        $payload = $service->payload([
            'escola' => 'EMEF Centro',
            'periodo' => 'Manha',
        ]);

        $this->assertCount(1, $payload['alunos']);
        $this->assertCount(1, $payload['linhas']);
        $this->assertSame('Ana Souza', $payload['alunos'][0]['nome']);
        $this->assertSame('Linha Centro', $payload['linhas'][0]['nome']);
        $this->assertSame('Periodo: Manha / Escola: EMEF Centro', $payload['filtro_descricao']);
    }

    public function testPayloadFiltraPorLinha(): void
    {
        $export = $this->createMock(TransporteEscolarExportService::class);
        $export->method('payload')->willReturn([
            'linhas' => [
                ['codigo' => 'TRE-01', 'nome' => 'Linha Centro', 'tipo' => 'proprio', 'unidade_escolar' => 'EMEF Centro', 'horario' => '06:40 / 11:30', 'custo' => 'R$ 3.420,00', 'pontos_total' => 2, 'roteiro_resumido' => 'Ponto Central -> Terminal'],
                ['codigo' => 'TRE-02', 'nome' => 'Linha Vila', 'tipo' => 'terceirizado', 'unidade_escolar' => 'EMEF Vila Nova', 'horario' => '05:55 / 12:10', 'custo' => 'R$ 8.150,00', 'pontos_total' => 1, 'roteiro_resumido' => 'Ponto Rural'],
            ],
            'veiculos' => [
                ['placa' => 'ABC1D23', 'modelo' => 'Microonibus', 'motorista' => 'Joao', 'status' => 'disponivel'],
            ],
            'alunos' => [
                ['cpf' => '12345678901', 'nome' => 'Ana Souza', 'escola' => 'EMEF Centro', 'linha' => 'TRE-01', 'embarque' => 'Rua A', 'periodo_uso' => 'Manha'],
                ['cpf' => '98765432100', 'nome' => 'Lucas Pereira', 'escola' => 'EMEF Vila Nova', 'linha' => 'TRE-02', 'embarque' => 'Rua B', 'periodo_uso' => 'Tarde'],
            ],
            'metadados' => [
                'gerado_em' => '2026-05-29T15:00:00-03:00',
                'fonte' => 'educacao_transporte_escolar',
                'versao' => 'v1',
            ],
        ]);

        $service = new TransporteEscolarRelatorioService($export);
        $payload = $service->payload([
            'linha' => 'TRE-01',
        ]);

        $this->assertCount(1, $payload['alunos']);
        $this->assertCount(1, $payload['linhas']);
        $this->assertSame('Ana Souza', $payload['alunos'][0]['nome']);
        $this->assertSame('Linha Centro', $payload['linhas'][0]['nome']);
        $this->assertSame('Linha: TRE-01', $payload['filtro_descricao']);
        $this->assertSame('EMEF Centro', $payload['alunos'][0]['unidade_escolar']);
        $this->assertSame('TRE-01', $payload['linha_selecionada']['codigo']);
        $this->assertSame('Ponto Central -> Terminal', $payload['linha_selecionada']['roteiro_resumido']);
    }
}
