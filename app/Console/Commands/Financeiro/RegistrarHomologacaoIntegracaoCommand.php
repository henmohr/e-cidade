<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\IntegracaoGovernamentalStatusService;
use Illuminate\Console\Command;

class RegistrarHomologacaoIntegracaoCommand extends Command
{
    protected $signature = 'financeiro:registrar-homologacao-integracao
                            {codigo : Codigo do registro de integracao}
                            {resultado : enviado|aceito|rejeitado}
                            {protocolo : Protocolo externo de homologacao}
                            {--mensagem= : Mensagem complementar}';

    protected $description = 'Registra resultado de homologacao externa de integracao com protocolo';

    public function handle(IntegracaoGovernamentalStatusService $service): int
    {
        $resultado = $service->registrarResultadoHomologacao(
            (int) $this->argument('codigo'),
            (string) $this->argument('resultado'),
            (string) $this->argument('protocolo'),
            $this->option('mensagem') ?: null
        );

        $this->line('Homologacao registrada');
        $this->line('Codigo: ' . $resultado['codigo']);
        $this->line('Sistema: ' . $resultado['sistema']);
        $this->line('Status: ' . $resultado['status_anterior'] . ' -> ' . $resultado['status_novo']);
        $this->line('Protocolo: ' . $resultado['protocolo_externo']);

        return self::SUCCESS;
    }
}
