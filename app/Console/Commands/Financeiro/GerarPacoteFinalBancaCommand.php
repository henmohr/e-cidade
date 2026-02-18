<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\PacoteFinalBancaService;
use Illuminate\Console\Command;

class GerarPacoteFinalBancaCommand extends Command
{
    protected $signature = 'financeiro:gerar-pacote-final-banca
                            {--matriz=docs/sprint9_matriz_status_licitacao.yml : Arquivo de matriz de cobertura}
                            {--anexos=docs/anexos_homologacao_assinados : Diretorio de anexos assinados}
                            {--saida=docs/pacote_final_banca : Diretorio de saida do pacote final}';

    protected $description = 'Gera pacote final de banca com manifesto consolidado e validacoes de prontidao';

    public function handle(PacoteFinalBancaService $service): int
    {
        $resultado = $service->gerar(
            (string) $this->option('matriz'),
            (string) $this->option('anexos'),
            (string) $this->option('saida')
        );

        $this->line('Pacote final de banca gerado');
        $this->line('Status final: ' . $resultado['status_final']);
        $this->line('Manifesto: ' . $resultado['manifesto']);
        $this->line('Resumo: ' . $resultado['resumo']);

        return self::SUCCESS;
    }
}
