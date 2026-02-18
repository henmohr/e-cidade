<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Relatorio\PacoteEvidenciasLicitacaoService;
use Illuminate\Console\Command;

class GerarPacoteEvidenciasLicitacaoCommand extends Command
{
    protected $signature = 'financeiro:gerar-pacote-evidencias
                            {--data-inicial= : Data inicial no formato YYYY-MM-DD}
                            {--data-final= : Data final no formato YYYY-MM-DD}
                            {--diretorio= : Diretorio de saida para o pacote}
                            {--sistemas= : Lista de sistemas separados por virgula (ex: SICONFI,TCE_PR,PORTAL_TRANSPARENCIA)}';

    protected $description = 'Gera pacote de evidencias da licitacao com manifesto, resumo e exportacoes financeiras';

    public function handle(PacoteEvidenciasLicitacaoService $service): int
    {
        $sistemasOption = (string) $this->option('sistemas');
        $sistemas = $sistemasOption !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $sistemasOption))))
            : null;

        $resultado = $service->gerar(
            $this->option('data-inicial') ?: null,
            $this->option('data-final') ?: null,
            $this->option('diretorio') ?: null,
            $sistemas
        );

        $this->line('Pacote de evidencias gerado com sucesso');
        $this->line('Diretorio: ' . $resultado['diretorio']);
        $this->line('Manifesto: ' . $resultado['manifesto']);
        $this->line('Resumo: ' . $resultado['resumo_markdown']);
        $this->line('Status recomendado: ' . $resultado['status_recomendado']);

        return self::SUCCESS;
    }
}
