<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use Illuminate\Console\Command;

class RelatorioHomologacaoIntegracoesCommand extends Command
{
    protected $signature = 'financeiro:relatorio-homologacao-integracoes
                            {--sistema= : Nome do sistema (ex: SICONFI, TCE_PR, PORTAL_TRANSPARENCIA)}
                            {--limite=200 : Limite de registros por status}';

    protected $description = 'Gera relatorio de homologacao externa por status das integracoes';

    public function handle(IntegracaoGovernamentalStatusService $service): int
    {
        $resumo = $service->gerarResumoHomologacao(
            $this->option('sistema') ?: null,
            (int) $this->option('limite')
        );

        $this->line('Relatorio de homologacao externa');
        $this->line('Sistema: ' . ($resumo['sistema'] ?: 'TODOS'));
        $this->line('Pendentes: ' . $resumo['totais']['pendente']);
        $this->line('Enviados: ' . $resumo['totais']['enviado']);
        $this->line('Aceitos: ' . $resumo['totais']['aceito']);
        $this->line('Rejeitados: ' . $resumo['totais']['rejeitado']);

        return self::SUCCESS;
    }
}
