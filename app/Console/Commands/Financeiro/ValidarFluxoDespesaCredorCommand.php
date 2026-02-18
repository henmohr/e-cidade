<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Credor\FluxoDespesaCredorService;
use Illuminate\Console\Command;
use LogicException;

class ValidarFluxoDespesaCredorCommand extends Command
{
    protected $signature = 'financeiro:validar-fluxo-despesa-credor
                            {numcgm : Codigo do credor (CGM)}
                            {empenho : Numero interno do empenho (e60_numemp)}';

    protected $description = 'Valida fluxo minimo de despesa: credor -> empenho -> atesto -> pagamento';

    public function handle(FluxoDespesaCredorService $service): int
    {
        $numcgm = (int) $this->argument('numcgm');
        $numeroEmpenho = (int) $this->argument('empenho');

        try {
            $resultado = $service->validarFluxoMinimo($numcgm, $numeroEmpenho);
        } catch (LogicException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->line('Status do fluxo: ' . $resultado['status']);
        $this->line('CGM: ' . $resultado['numcgm']);
        $this->line('Empenho: ' . $resultado['empenho']);
        $this->line('Etapas:');
        $this->line('- Credor: ' . ($resultado['etapas']['credor'] ? 'OK' : 'PENDENTE'));
        $this->line('- Empenho: ' . ($resultado['etapas']['empenho'] ? 'OK' : 'PENDENTE'));
        $this->line('- Atesto: ' . ($resultado['etapas']['atesto'] ? 'OK' : 'PENDENTE'));
        $this->line('- Pagamento: ' . ($resultado['etapas']['pagamento'] ? 'OK' : 'PENDENTE'));

        if (!empty($resultado['pendencias'])) {
            $this->line('Pendencias:');
            foreach ($resultado['pendencias'] as $pendencia) {
                $this->line('- ' . $pendencia);
            }
        }

        return $resultado['status'] === 'FLUXO_COMPLETO' ? self::SUCCESS : self::FAILURE;
    }
}

