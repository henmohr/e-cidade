<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\ExportacaoRelatoriosFinanceirosService;
use Illuminate\Console\Command;

class ExportarRelatoriosFinanceirosCommand extends Command
{
    protected $signature = 'financeiro:exportar-relatorios
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}
                            {--diretorio= : Diretorio base para exportacao}';

    protected $description = 'Exporta relatorios financeiros em PDF e planilha CSV';

    public function handle(ExportacaoRelatoriosFinanceirosService $service): int
    {
        $resultado = $service->exportar(
            $this->option('data-inicial') ?: null,
            $this->option('data-final') ?: null,
            $this->option('diretorio') ?: null
        );

        $this->line('Arquivos exportados com sucesso');
        $this->line('Planilha CSV: ' . $resultado['arquivo_planilha_csv']);
        $this->line('PDF: ' . $resultado['arquivo_pdf']);

        return self::SUCCESS;
    }
}
