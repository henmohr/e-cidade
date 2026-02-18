<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\BalancoService;
use Illuminate\Console\Command;

class GerarBalancosCommand extends Command
{
    protected $signature = 'financeiro:gerar-balancos
                            {--tipo=todos : patrimonial|orcamentario|financeiro|todos}
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}';

    protected $description = 'Gera balancos obrigatorios (patrimonial, orcamentario e financeiro)';

    public function handle(BalancoService $service): int
    {
        $tipo = (string) $this->option('tipo');
        $dataInicial = $this->option('data-inicial') ?: null;
        $dataFinal = $this->option('data-final') ?: null;

        $resultado = $service->gerar($tipo, $dataInicial, $dataFinal);

        $this->line('Balancos gerados');
        $this->line('Tipo: ' . $resultado['tipo']);
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
