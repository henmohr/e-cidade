<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\PublicacaoPortalTransparenciaService;
use Illuminate\Console\Command;

class PublicarPortalTransparenciaCommand extends Command
{
    protected $signature = 'financeiro:publicar-portal-transparencia
                            {--data-referencia= : Data de corte no formato YYYY-MM-DD}';

    protected $description = 'Publica consolidado de receitas, despesas e contratos no Portal da Transparencia';

    public function handle(PublicacaoPortalTransparenciaService $service): int
    {
        $dataReferencia = $this->option('data-referencia') ?: null;
        $resultado = $service->publicar($dataReferencia);

        $this->line('Publicacao de transparencia registrada');
        $this->line('Codigo: ' . $resultado['codigo_publicacao']);
        $this->line('Data referencia: ' . $resultado['data_referencia']);
        $this->line('Receitas: ' . number_format((float) $resultado['resumo']['receitas']['valor_total'], 2, '.', ''));
        $this->line('Despesas: ' . number_format((float) $resultado['resumo']['despesas']['valor_total'], 2, '.', ''));
        $this->line('Contratos: ' . number_format((float) $resultado['resumo']['contratos']['valor_total'], 2, '.', ''));

        return self::SUCCESS;
    }
}
