<?php

namespace Database\Seeders;

use App\Models\Educacao\TransporteEscolar\AlunoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\LinhaVeiculoTransporteEscolar;
use App\Models\Educacao\TransporteEscolar\VeiculoTransporteEscolar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransporteEscolarSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $linhaCentro = LinhaTransporteEscolar::query()->updateOrCreate(
                ['codigo' => 'TRE-01'],
                [
                    'nome' => 'Linha Centro - EMEI Esperanca',
                    'tipo_servico' => 'proprio',
                    'horario_saida' => '06:40',
                    'horario_retorno' => '11:30',
                    'custo_mensal' => 3420.00,
                    'unidade_escolar' => 'EMEI Esperanca',
                    'rota_descricao' => 'Centro -> Vila Nova -> EMEI Esperanca',
                    'ativo' => true,
                ]
            );

            $linhaRural = LinhaTransporteEscolar::query()->updateOrCreate(
                ['codigo' => 'TRE-02'],
                [
                    'nome' => 'Linha Rural - EMEF Vila Nova',
                    'tipo_servico' => 'terceirizado',
                    'horario_saida' => '05:55',
                    'horario_retorno' => '12:10',
                    'custo_mensal' => 8150.00,
                    'unidade_escolar' => 'EMEF Vila Nova',
                    'rota_descricao' => 'Zona rural leste com paradas comunitarias',
                    'ativo' => true,
                ]
            );

            $veiculoMicro = VeiculoTransporteEscolar::query()->updateOrCreate(
                ['placa' => 'RIO1A23'],
                [
                    'modelo' => 'Microonibus 28 lugares',
                    'motorista_nome' => 'Carlos Henrique',
                    'capacidade' => 28,
                    'situacao' => 'disponivel',
                    'observacao' => 'Veiculo proprio da rede',
                ]
            );

            $veiculoOnibus = VeiculoTransporteEscolar::query()->updateOrCreate(
                ['placa' => 'QWE4Z66'],
                [
                    'modelo' => 'Onibus escolar 44 lugares',
                    'motorista_nome' => 'Marcos Vinicius',
                    'capacidade' => 44,
                    'situacao' => 'em_rota',
                    'observacao' => 'Prestacao terceirizada',
                ]
            );

            LinhaVeiculoTransporteEscolar::query()->updateOrCreate(
                ['linha_id' => $linhaCentro->id, 'veiculo_id' => $veiculoMicro->id],
                [
                    'data_inicio' => now()->toDateString(),
                    'observacao' => 'Vinculo inicial de homologacao',
                ]
            );

            LinhaVeiculoTransporteEscolar::query()->updateOrCreate(
                ['linha_id' => $linhaRural->id, 'veiculo_id' => $veiculoOnibus->id],
                [
                    'data_inicio' => now()->toDateString(),
                    'observacao' => 'Vinculo de terceirizado',
                ]
            );

            AlunoTransporteEscolar::query()->updateOrCreate(
                ['aluno_cpf' => '12345678901'],
                [
                    'linha_id' => $linhaCentro->id,
                    'aluno_nome' => 'Ana Souza',
                    'escola_nome' => 'EMEI Esperanca',
                    'local_embarque' => 'Rua Central, 101',
                    'motivo_uso' => 'Distancia domiciliar',
                    'periodo_uso' => '2026',
                    'situacao_matricula' => 'ativa',
                    'utiliza_transporte' => true,
                ]
            );

            AlunoTransporteEscolar::query()->updateOrCreate(
                ['aluno_cpf' => '98765432100'],
                [
                    'linha_id' => $linhaRural->id,
                    'aluno_nome' => 'Lucas Pereira',
                    'escola_nome' => 'EMEF Vila Nova',
                    'local_embarque' => 'Rodovia Rural km 7',
                    'motivo_uso' => 'Zona rural',
                    'periodo_uso' => '2026',
                    'situacao_matricula' => 'ativa',
                    'utiliza_transporte' => true,
                ]
            );
        });
    }
}
