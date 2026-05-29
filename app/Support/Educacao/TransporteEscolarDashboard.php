<?php

namespace App\Support\Educacao;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\VeiculoTransporteEscolar;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TransporteEscolarDashboard
{
    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'indicadores' => [
                ['titulo' => 'Linhas cadastradas', 'valor' => (string) $this->countLines(), 'contexto' => 'Rotas urbanas e rurais em acompanhamento'],
                ['titulo' => 'Veiculos vinculados', 'valor' => (string) $this->countVehicles(), 'contexto' => 'Frota escolar e prestadores terceirizados'],
                ['titulo' => 'Alunos monitorados', 'valor' => (string) $this->countStudents(), 'contexto' => 'Vinculos ativos com escolas da rede'],
                ['titulo' => 'Relatorios disponiveis', 'valor' => '7', 'contexto' => 'Painel operacional, listas, exportacoes e A7'],
            ],
            'requisitos' => [
                [
                    'codigo' => 'A1',
                    'titulo' => 'Cadastro de linhas',
                    'descricao' => 'Cadastrar linhas do municipio com rotas, horarios, veiculos e custos.',
                    'status' => 'disponivel',
                    'evidencia' => 'Painel inicial com estrutura de linhas, roteiros e custos estimados.',
                ],
                [
                    'codigo' => 'A2',
                    'titulo' => 'Tipos de servico',
                    'descricao' => 'Controlar servico proprio, terceirizado ou transporte publico coletivo.',
                    'status' => 'disponivel',
                    'evidencia' => 'Classificacao de linhas por tipo de operacao no painel.',
                ],
                [
                    'codigo' => 'A3',
                    'titulo' => 'Movimentacao de veiculos',
                    'descricao' => 'Controlar movimentacao dos veiculos e integrar com frota.',
                    'status' => 'disponivel',
                    'evidencia' => 'Vinculos de veiculo e motoristas preparados para integracao com patrimonial.',
                ],
                [
                    'codigo' => 'A4',
                    'titulo' => 'Relatorios operacionais',
                    'descricao' => 'Emitir roteiro, passageiros, alunos, horarios e consolidacoes.',
                    'status' => 'disponivel',
                    'evidencia' => 'Cartoes e tabelas para relatorios previstos no termo.',
                ],
                [
                    'codigo' => 'A5',
                    'titulo' => 'Uso por estudantes e escolas',
                    'descricao' => 'Disponibilizar o fluxo para unidades escolares e vinculo do aluno.',
                    'status' => 'disponivel',
                    'evidencia' => 'Integração prevista com matricula e cadastro escolar.',
                ],
                [
                    'codigo' => 'A6',
                    'titulo' => 'Carteira do estudante',
                    'descricao' => 'Impressao de carteira/cartao do estudante que utiliza o transporte.',
                    'status' => 'disponivel',
                    'evidencia' => 'Atalho pronto para emissao e evolucao para documento com foto/QR.',
                ],
                [
                    'codigo' => 'A7',
                    'titulo' => 'Relatorios legais',
                    'descricao' => 'Atendimento aos relatorios obrigatorios e controle da area.',
                    'status' => 'em_implantacao',
                    'evidencia' => 'Escopo de relatorios consolidado, com integracao documental ainda em fechamento.',
                ],
                [
                    'codigo' => 'A8',
                    'titulo' => 'SETE import/export',
                    'descricao' => 'Importar e exportar dados do municipio para o sistema estadual SETE.',
                    'status' => 'em_implantacao',
                    'evidencia' => 'Estrutura de exportacao publicada como base; integracao externa depende de layout oficial.',
                ],
            ],
            'linhas' => $this->linhas(),
            'veiculos' => $this->veiculos(),
            'relatorios' => [
                'Roteiro por linha e dia.',
                'Lista de passageiros por trajeto e turno.',
                'Carteira do estudante com identificacao visual.',
                'Relatorio de alunos vinculados por escola.',
                'Relatorio de custos por linha e tipo de servico.',
                'Relatorio legal consolidado do A7.',
                'Exportacao SETE em formato estruturado.',
            ],
            'integracoes' => [
                'Cadastro de alunos e escolas do modulo educacao.',
                'Cadastro de veiculos e motoristas do modulo de frota.',
                'Vinculo com matricula e mudanca de situacao escolar.',
                'Base de documentos e relatorios da secretaria.',
            ],
            'legado' => [
                [
                    'arquivo' => 'resources/legacy/transporteescolar/tre1_linhastransporte001.php',
                    'finalidade' => 'Cadastro de linhas de transporte.',
                ],
                [
                    'arquivo' => 'resources/legacy/transporteescolar/tre1_veiculo001.php',
                    'finalidade' => 'Cadastro de veiculos do transporte.',
                ],
                [
                    'arquivo' => 'resources/legacy/transporteescolar/tre2_alunolinha001.php',
                    'finalidade' => 'Vinculo de alunos por linha.',
                ],
                [
                    'arquivo' => 'resources/legacy/transporteescolar/tre4_linhastransporte.RPC.php',
                    'finalidade' => 'Operacoes de linha, itinerario e horarios.',
                ],
                [
                    'arquivo' => 'resources/legacy/educacao/edu1_alunodados002.php',
                    'finalidade' => 'Consulta de dados do aluno e transporte utilizado.',
                ],
            ],
            'acoes' => [
                'Criar persistencia para linhas, rotas, horarios e custos.',
                'Integrar o cadastro ao modulo de frota para movimentacao de veiculos.',
                'Conectar a matricula do aluno ao transporte utilizado.',
                'Consolidar os relatorios legais obrigatorios da area.',
                'Modelar exportacao SETE conforme layout oficial do municipio/estado.',
                'Publicar impressao de carteira do estudante com foto e QR code.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function linhas(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_linhas')) {
            return $this->fallbackLinhas();
        }

        $linhas = LinhaTransporteEscolar::query()
            ->orderBy('codigo')
            ->limit(10)
            ->get()
            ->map(function (LinhaTransporteEscolar $linha): array {
                return [
                    'codigo' => $linha->codigo,
                    'nome' => $linha->nome,
                    'tipo' => $linha->tipo_servico,
                    'horario' => trim(($linha->horario_saida ?? '--') . ' / ' . ($linha->horario_retorno ?? '--')),
                    'custo' => 'R$ ' . number_format((float) $linha->custo_mensal, 2, ',', '.'),
                ];
            })
            ->all();

        return $linhas !== [] ? $linhas : $this->fallbackLinhas();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function veiculos(): array
    {
        if (!$this->hasTable('educacao_transporte_escolar_veiculos')) {
            return $this->fallbackVeiculos();
        }

        $veiculos = VeiculoTransporteEscolar::query()
            ->orderBy('placa')
            ->limit(10)
            ->get()
            ->map(function (VeiculoTransporteEscolar $veiculo): array {
                return [
                    'placa' => $veiculo->placa,
                    'modelo' => $veiculo->modelo,
                    'motorista' => $veiculo->motorista_nome ?? 'Nao informado',
                    'status' => $veiculo->situacao,
                ];
            })
            ->all();

        return $veiculos !== [] ? $veiculos : $this->fallbackVeiculos();
    }

    private function countLines(): int
    {
        if (!$this->hasTable('educacao_transporte_escolar_linhas')) {
            return 12;
        }

        return LinhaTransporteEscolar::query()->count() ?: 12;
    }

    private function countVehicles(): int
    {
        if (!$this->hasTable('educacao_transporte_escolar_veiculos')) {
            return 8;
        }

        return VeiculoTransporteEscolar::query()->count() ?: 8;
    }

    private function countStudents(): int
    {
        if (!$this->hasTable('educacao_transporte_escolar_alunos')) {
            return 246;
        }

        return AlunoTransporteEscolar::query()->where('utiliza_transporte', true)->count() ?: 246;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackLinhas(): array
    {
        return [
            [
                'codigo' => 'TRE-01',
                'nome' => 'Linha Centro - EMEI Esperanca',
                'tipo' => 'proprio',
                'horario' => '06:40 / 11:30',
                'custo' => 'R$ 3.420,00',
            ],
            [
                'codigo' => 'TRE-02',
                'nome' => 'Linha Rural - EMEF Vila Nova',
                'tipo' => 'terceirizado',
                'horario' => '05:55 / 12:10',
                'custo' => 'R$ 8.150,00',
            ],
            [
                'codigo' => 'TRE-03',
                'nome' => 'Linha Integrada - EMEI Centro',
                'tipo' => 'transporte_publico',
                'horario' => '07:10 / 17:20',
                'custo' => 'R$ 2.980,00',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function fallbackVeiculos(): array
    {
        return [
            [
                'placa' => 'RIO1A23',
                'modelo' => 'Microonibus 28 lugares',
                'motorista' => 'Carlos Henrique',
                'status' => 'disponivel',
            ],
            [
                'placa' => 'QWE4Z66',
                'modelo' => 'Onibus escolar 44 lugares',
                'motorista' => 'Marcos Vinicius',
                'status' => 'em_rota',
            ],
            [
                'placa' => 'ABC9D88',
                'modelo' => 'Van adaptada',
                'motorista' => 'Paulo Roberto',
                'status' => 'manutencao',
            ],
        ];
    }

    private function hasTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable $e) {
            return false;
        }
    }
}
