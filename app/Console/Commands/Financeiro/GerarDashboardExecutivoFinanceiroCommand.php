<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\DashboardExecutivoFinanceiroService;
use Illuminate\Console\Command;

class GerarDashboardExecutivoFinanceiroCommand extends Command
{
    protected $signature = 'financeiro:dashboard-executivo
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}';

    protected $description = 'Gera dashboard executivo de receitas, despesas, execucao orcamentaria e alertas';

    public function handle(DashboardExecutivoFinanceiroService $service): int
    {
        $resultado = $service->gerar(
            $this->option('data-inicial') ?: null,
            $this->option('data-final') ?: null
        );

        $this->line('Dashboard executivo financeiro');
        $this->line('Periodo: ' . ($resultado['periodo']['data_inicial'] ?? 'inicio') . ' a ' . ($resultado['periodo']['data_final'] ?? 'fim'));
        $this->line('Receitas realizadas: ' . number_format((float) ($resultado['painel_receitas']['realizado'] ?? 0), 2, '.', ''));
        $this->line('Despesas empenhadas: ' . number_format((float) ($resultado['painel_despesas']['empenhadas'] ?? 0), 2, '.', ''));
        $this->line('Execucao: ' . number_format((float) ($resultado['execucao_orcamentaria']['percentual_execucao'] ?? 0), 2, '.', '') . '%');
        $this->line('Alertas ativos: ' . count($resultado['alertas'] ?? []));

        return self::SUCCESS;
    }
}
