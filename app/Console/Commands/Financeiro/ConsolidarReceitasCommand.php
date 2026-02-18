<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Receita\ControleReceitasService;
use Illuminate\Console\Command;

class ConsolidarReceitasCommand extends Command
{
    protected $signature = 'financeiro:consolidar-receitas
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}';

    protected $description = 'Consolida receitas tributarias e transferencias intergovernamentais';

    public function handle(ControleReceitasService $service): int
    {
        $dataInicial = $this->option('data-inicial');
        $dataFinal = $this->option('data-final');
        $resultado = $service->consolidar($dataInicial ?: null, $dataFinal ?: null);

        $this->line('Consolidado de receitas');
        $this->line('Periodo: ' . ($resultado['periodo']['data_inicial'] ?? 'inicio') . ' a ' . ($resultado['periodo']['data_final'] ?? 'fim'));
        $this->line('Total tributarias: ' . number_format((float) $resultado['totais']['tributarias'], 2, '.', ''));
        $this->line('Total transferencias intergovernamentais: ' . number_format((float) $resultado['totais']['transferencias_intergovernamentais'], 2, '.', ''));
        $this->line('Total demais receitas: ' . number_format((float) $resultado['totais']['demais_receitas'], 2, '.', ''));

        return self::SUCCESS;
    }
}

