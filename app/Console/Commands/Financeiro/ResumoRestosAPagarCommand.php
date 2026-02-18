<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Tesouraria\RestosAPagarService;
use Illuminate\Console\Command;

class ResumoRestosAPagarCommand extends Command
{
    protected $signature = 'financeiro:resumo-restos-a-pagar
                            {--ano= : Exercicio para consolidacao de restos a pagar}';

    protected $description = 'Consolida restos a pagar processados e nao processados';

    public function handle(RestosAPagarService $service): int
    {
        $ano = $this->option('ano');
        $resumo = $service->obterResumo($ano !== null ? (int) $ano : null);

        $this->line('Resumo de restos a pagar');
        $this->line('Ano: ' . $resumo['ano']);
        $this->line('Processados: ' . number_format((float) $resumo['restos_processados'], 2, '.', ''));
        $this->line('Nao processados: ' . number_format((float) $resumo['restos_nao_processados'], 2, '.', ''));
        $this->line('Total: ' . number_format((float) $resumo['restos_total'], 2, '.', ''));

        return self::SUCCESS;
    }
}

