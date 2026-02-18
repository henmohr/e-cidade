<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Credor\ValidacaoCredorService;
use Illuminate\Console\Command;

class ValidarCredorCommand extends Command
{
    protected $signature = 'financeiro:validar-credor
                            {numcgm : Codigo do CGM do credor}';

    protected $description = 'Valida cadastro de credor por CPF/CNPJ e pendencias documentais';

    public function handle(ValidacaoCredorService $service): int
    {
        $numcgm = (int) $this->argument('numcgm');
        $resultado = $service->validarPorCgm($numcgm);

        $this->line('Status: ' . $resultado['status']);
        $this->line('CGM: ' . $resultado['numcgm']);

        if (!empty($resultado['dados'])) {
            $this->line('Nome: ' . $resultado['dados']['nome']);
            $this->line('Documento: ' . $resultado['dados']['documento']);
        }

        if (!empty($resultado['pendencias'])) {
            $this->line('Pendencias:');
            foreach ($resultado['pendencias'] as $pendencia) {
                $this->line('- ' . $pendencia);
            }
        }

        return ($resultado['apto'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}

