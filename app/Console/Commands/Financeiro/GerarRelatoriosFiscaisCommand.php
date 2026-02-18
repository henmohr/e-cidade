<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\RelatorioFiscalService;
use Illuminate\Console\Command;

class GerarRelatoriosFiscaisCommand extends Command
{
    protected $signature = 'financeiro:gerar-relatorios-fiscais
                            {--tipo=todos : rgf|rreo|todos}
                            {--periodicidade=quadrimestral : mensal|quadrimestral}
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}';

    protected $description = 'Gera RGF e RREO conforme periodicidade configurada';

    public function handle(RelatorioFiscalService $service): int
    {
        $resultado = $service->gerar(
            (string) $this->option('tipo'),
            (string) $this->option('periodicidade'),
            $this->option('data-inicial') ?: null,
            $this->option('data-final') ?: null
        );

        $this->line('Relatorios fiscais gerados');
        $this->line('Tipo: ' . $resultado['tipo']);
        $this->line('Periodicidade: ' . $resultado['periodicidade']);
        $this->line('Periodo: ' . ($resultado['periodo']['data_inicial'] ?? 'inicio') . ' a ' . ($resultado['periodo']['data_final'] ?? 'fim'));

        foreach ($resultado['relatorios'] as $nome => $dados) {
            $this->line('[' . strtoupper((string) $nome) . ']');

            foreach ($dados as $chave => $valor) {
                $this->line($chave . ': ' . number_format((float) $valor, 2, '.', ''));
            }
        }

        return self::SUCCESS;
    }
}
