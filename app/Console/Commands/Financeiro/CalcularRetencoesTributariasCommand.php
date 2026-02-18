<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Retencoes\CalculadoraRetencoesTributariasService;
use Illuminate\Console\Command;
use LogicException;

class CalcularRetencoesTributariasCommand extends Command
{
    protected $signature = 'financeiro:calcular-retencoes
                            {valor-bruto : Valor bruto da despesa}
                            {--irrf=1.50 : Aliquota de IRRF em percentual}
                            {--iss=2.00 : Aliquota de ISS em percentual}
                            {--inss=11.00 : Aliquota de INSS em percentual}';

    protected $description = 'Calcula retencoes tributarias (IRRF, ISS, INSS) e valor liquido da despesa';

    public function handle(CalculadoraRetencoesTributariasService $service): int
    {
        $valorBruto = (float) $this->argument('valor-bruto');
        $aliquotas = [
            'irrf' => (float) $this->option('irrf'),
            'iss' => (float) $this->option('iss'),
            'inss' => (float) $this->option('inss'),
        ];

        try {
            $resultado = $service->calcular($valorBruto, $aliquotas);
        } catch (LogicException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $this->line('Valor bruto: ' . number_format((float) $resultado['valor_bruto'], 2, '.', ''));
        $this->line('Retencoes:');
        foreach ($resultado['retencoes'] as $tributo => $dados) {
            $aliquota = $dados['aliquota'] !== null ? number_format((float) $dados['aliquota'], 2, '.', '') . '%' : 'N/A';
            $valor = number_format((float) $dados['valor'], 2, '.', '');
            $this->line("- {$tributo}: aliquota {$aliquota} | valor {$valor}");
        }
        $this->line('Total retido: ' . number_format((float) $resultado['total_retencoes'], 2, '.', ''));
        $this->line('Valor liquido: ' . number_format((float) $resultado['valor_liquido'], 2, '.', ''));

        return self::SUCCESS;
    }
}

