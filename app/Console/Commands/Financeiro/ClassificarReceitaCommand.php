<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Receita\ClassificacaoReceitaService;
use Illuminate\Console\Command;

class ClassificarReceitaCommand extends Command
{
    protected $signature = 'financeiro:classificar-receita
                            {codigo-receita : Codigo da receita para classificar}';

    protected $description = 'Classifica receita em corrente ou capital (incluindo intraorcamentaria)';

    public function handle(ClassificacaoReceitaService $service): int
    {
        $codigoReceita = (int) $this->argument('codigo-receita');
        $resultado = $service->classificarPorCodigo($codigoReceita);

        $this->line('Codigo: ' . $resultado['codigo_receita']);
        $this->line('Status: ' . $resultado['status']);

        if (!empty($resultado['grupo'])) {
            $this->line('Grupo: ' . $resultado['grupo']);
            $this->line('Subgrupo: ' . $resultado['subgrupo']);
        }

        if (!empty($resultado['descricao'])) {
            $this->line('Descricao: ' . $resultado['descricao']);
        }

        return $resultado['status'] === 'CLASSIFICADA' ? self::SUCCESS : self::FAILURE;
    }
}

