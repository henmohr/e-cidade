<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use Illuminate\Console\Command;

class MonitorarIntegracoesGovernamentaisCommand extends Command
{
    protected $signature = 'financeiro:monitorar-integracoes
                            {--sistema= : Nome do sistema/integracao (ex: SICONFI, TCE_PR)}
                            {--limite=50 : Quantidade maxima de registros}
                            {--reprocessar : Reprocessa automaticamente registros rejeitados}';

    protected $description = 'Monitora falhas e reprocessamento de integracoes governamentais';

    public function handle(IntegracaoGovernamentalStatusService $service): int
    {
        $sistema = $this->option('sistema') ?: null;
        $limite = (int) $this->option('limite');

        $falhas = $service->monitorarFalhas($sistema, $limite);

        $this->line('Monitoramento de integracoes governamentais');
        $this->line('Sistema: ' . ($sistema ?: 'TODOS'));
        $this->line('Falhas identificadas: ' . $falhas['total_falhas']);

        if (!$this->option('reprocessar')) {
            return self::SUCCESS;
        }

        $reprocessamento = $service->reprocessarFalhas($sistema, $limite);
        $this->line('Registros reprocessados: ' . $reprocessamento['total_reprocessados']);

        return self::SUCCESS;
    }
}
