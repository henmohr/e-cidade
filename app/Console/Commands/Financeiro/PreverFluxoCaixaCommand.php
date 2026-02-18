<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Tesouraria\FluxoCaixaService;
use Illuminate\Console\Command;
use LogicException;

class PreverFluxoCaixaCommand extends Command
{
    protected $signature = 'financeiro:prever-fluxo-caixa
                            {conta : Codigo da conta bancaria (k13_conta)}
                            {reduz : Codigo reduzido da conta plano (k13_reduz)}
                            {--data-base= : Data base da previsao (YYYY-MM-DD)}
                            {--programar-valor= : Valor de pagamento para validar programacao}
                            {--programar-data= : Data do pagamento programado (YYYY-MM-DD)}';

    protected $description = 'Gera previsao de fluxo de caixa em 7 dias e valida programacao financeira';

    public function handle(FluxoCaixaService $service): int
    {
        $conta = (int) $this->argument('conta');
        $reduz = (int) $this->argument('reduz');
        $dataBase = $this->option('data-base');

        try {
            $projecao = $service->projetar7Dias($conta, $reduz, $dataBase ?: null);
        } catch (LogicException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->line('Previsao de fluxo de caixa (7 dias):');
        foreach ($projecao['dias'] as $dia) {
            $this->line(
                sprintf(
                    '%s | inicial: %.2f | entradas: %.2f | saidas: %.2f | final: %.2f',
                    $dia['data'],
                    $dia['saldo_inicial'],
                    $dia['entradas_previstas'],
                    $dia['saidas_previstas'],
                    $dia['saldo_final']
                )
            );
        }

        $this->line('Menor saldo previsto: ' . number_format($projecao['menor_saldo_previsto'], 2, '.', ''));
        $this->line('Saldo final previsto: ' . number_format($projecao['saldo_final_previsto'], 2, '.', ''));

        $valorProgramado = $this->option('programar-valor');
        $dataProgramada = $this->option('programar-data');

        if ($valorProgramado !== null || $dataProgramada !== null) {
            if ($valorProgramado === null || $dataProgramada === null) {
                $this->error('Para validar programacao financeira, informe --programar-valor e --programar-data juntos.');
                return self::FAILURE;
            }

            try {
                $service->assertPodeProgramarPagamento(
                    $conta,
                    $reduz,
                    (float) $valorProgramado,
                    (string) $dataProgramada,
                    $dataBase ?: null
                );
                $this->info('Programacao financeira: OK');
            } catch (LogicException $exception) {
                $this->error($exception->getMessage());
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}

