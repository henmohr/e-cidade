<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\DemonstracaoContabilService;
use Illuminate\Console\Command;

class GerarDemonstracoesContabeisCommand extends Command
{
    protected $signature = 'financeiro:gerar-demonstracoes
                            {--tipo=todas : dvp|dfc|todas}
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}';

    protected $description = 'Gera demonstracoes contabil obrigatorias (DVP e DFC)';

    public function handle(DemonstracaoContabilService $service): int
    {
        $tipo = (string) $this->option('tipo');
        $dataInicial = $this->option('data-inicial') ?: null;
        $dataFinal = $this->option('data-final') ?: null;

        $resultado = $service->gerar($tipo, $dataInicial, $dataFinal);

        $this->line('Demonstracoes geradas');
        $this->line('Tipo: ' . $resultado['tipo']);
        $this->line('Periodo: ' . ($resultado['periodo']['data_inicial'] ?? 'inicio') . ' a ' . ($resultado['periodo']['data_final'] ?? 'fim'));

        foreach ($resultado['demonstracoes'] as $nome => $dados) {
            $this->line('[' . strtoupper((string) $nome) . ']');

            foreach ($dados as $chave => $valor) {
                $this->line($chave . ': ' . number_format((float) $valor, 2, '.', ''));
            }
        }

        return self::SUCCESS;
    }
}
