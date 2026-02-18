<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Tesouraria\DashboardTesourariaService;
use Illuminate\Console\Command;
use LogicException;

class DashboardTesourariaCommand extends Command
{
    protected $signature = 'financeiro:dashboard-tesouraria
                            {conta : Codigo da conta bancaria (k13_conta)}
                            {reduz : Codigo reduzido da conta plano (k13_reduz)}
                            {--ano= : Exercicio para consolidacao de restos a pagar}
                            {--data-base= : Data base da projecao (YYYY-MM-DD)}';

    protected $description = 'Exibe dashboard de tesouraria com saldo atual, projecao de 7 dias e alertas';

    public function handle(DashboardTesourariaService $service): int
    {
        $conta = (int) $this->argument('conta');
        $reduz = (int) $this->argument('reduz');
        $ano = $this->option('ano');
        $dataBase = $this->option('data-base');

        try {
            $dashboard = $service->gerar(
                $conta,
                $reduz,
                $ano !== null ? (int) $ano : null,
                $dataBase ?: null
            );
        } catch (LogicException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->line('Dashboard Tesouraria');
        $this->line('Atualizado em: ' . $dashboard['atualizado_em']);
        $this->line('Saldo atual: ' . number_format((float) $dashboard['saldo_atual'], 2, '.', ''));
        $this->line('Saldo final previsto (7 dias): ' . number_format((float) $dashboard['projecao_7_dias']['saldo_final_previsto'], 2, '.', ''));
        $this->line('Menor saldo previsto: ' . number_format((float) $dashboard['projecao_7_dias']['menor_saldo_previsto'], 2, '.', ''));
        $this->line('Restos processados: ' . number_format((float) $dashboard['restos_a_pagar']['restos_processados'], 2, '.', ''));
        $this->line('Restos nao processados: ' . number_format((float) $dashboard['restos_a_pagar']['restos_nao_processados'], 2, '.', ''));

        if (!empty($dashboard['alertas'])) {
            $this->line('Alertas:');
            foreach ($dashboard['alertas'] as $alerta) {
                $this->line('- ' . $alerta);
            }
            return self::FAILURE;
        }

        $this->info('Sem alertas de tesouraria para a janela atual.');
        return self::SUCCESS;
    }
}

