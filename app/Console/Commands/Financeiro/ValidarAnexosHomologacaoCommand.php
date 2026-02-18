<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\HomologacaoAnexosService;
use Illuminate\Console\Command;

class ValidarAnexosHomologacaoCommand extends Command
{
    protected $signature = 'financeiro:validar-anexos-homologacao
                            {--diretorio=docs/anexos_homologacao_assinados : Diretorio com anexos assinados}';

    protected $description = 'Valida anexos assinados de homologacao externa para o pacote documental';

    public function handle(HomologacaoAnexosService $service): int
    {
        $diretorio = (string) $this->option('diretorio');
        $resultado = $service->validarDiretorio($diretorio);

        $this->line('Validacao de anexos de homologacao');
        $this->line('Diretorio: ' . $resultado['diretorio']);
        $this->line('Status: ' . $resultado['status']);
        $this->line('Presentes: ' . count($resultado['presentes']));
        $this->line('Ausentes: ' . count($resultado['ausentes']));
        $this->line('Vazios: ' . count($resultado['vazios']));

        if ($resultado['status'] !== 'ok') {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
