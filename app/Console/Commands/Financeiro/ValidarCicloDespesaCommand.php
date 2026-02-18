<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use LogicException;
use Illuminate\Console\Command;

class ValidarCicloDespesaCommand extends Command
{
    protected $signature = 'financeiro:validar-ciclo-despesa
                            {empenho : Numero interno do empenho (e60_numemp)}
                            {--ordem= : Codigo da ordem de pagamento (e50_codord)}
                            {--dotacao= : Codigo da dotacao para validar fixacao}
                            {--ano= : Ano da dotacao para validar fixacao}
                            {--valor-empenho= : Valor para validar disponibilidade da dotacao}
                            {--expect-payment-block : Espera bloqueio na regra de pagamento (cenario negativo)}';

    protected $description = 'Valida regras do ciclo da despesa: fixacao, empenho, liquidacao e pagamento';

    public function handle(CicloDespesaService $service): int
    {
        $numeroEmpenho = (int) $this->argument('empenho');
        $codigoOrdem = $this->option('ordem');
        $dotacao = $this->option('dotacao');
        $ano = $this->option('ano');
        $valorEmpenho = $this->option('valor-empenho');
        $expectPaymentBlock = (bool) $this->option('expect-payment-block');

        if ($dotacao !== null || $ano !== null) {
            if ($dotacao === null || $ano === null) {
                $this->error('Para validar fixacao, informe --dotacao e --ano juntos.');
                return self::FAILURE;
            }

            try {
                $service->assertPodeEmpenhar(
                    (int) $ano,
                    (int) $dotacao,
                    $valorEmpenho !== null ? (float) $valorEmpenho : null
                );
                $this->info('Fixacao valida para dotacao informada.');
            } catch (LogicException $exception) {
                $this->error($exception->getMessage());
                return self::FAILURE;
            }
        }

        $situacao = $service->obterSituacao($numeroEmpenho);

        $this->line('Situacao do ciclo para o empenho informado:');
        $this->line('- Empenho: ' . ($situacao['empenho'] ? 'OK' : 'PENDENTE'));
        $this->line('- Liquidacao: ' . ($situacao['liquidacao'] ? 'OK' : 'PENDENTE'));
        $this->line('- Pagamento: ' . ($situacao['pagamento'] ? 'OK' : 'PENDENTE'));

        try {
            $service->assertPodeLiquidar($numeroEmpenho);
            $this->info('Regra de liquidacao: OK');
        } catch (LogicException $exception) {
            $this->warn('Regra de liquidacao: ' . $exception->getMessage());
        }

        try {
            $service->assertSequenciaObrigatoria($numeroEmpenho);
            $this->info('Regra de sequencia obrigatoria: OK');
        } catch (LogicException $exception) {
            $this->warn('Regra de sequencia obrigatoria: ' . $exception->getMessage());
        }

        try {
            $service->assertPodePagar($numeroEmpenho);
            if ($expectPaymentBlock && empty($codigoOrdem)) {
                $this->error('Regra de pagamento: era esperado bloqueio, mas a validacao por empenho passou.');
                return self::FAILURE;
            }
            $this->info('Regra de pagamento (por empenho): OK');
        } catch (LogicException $exception) {
            if ($expectPaymentBlock && empty($codigoOrdem)) {
                $this->info('Regra de pagamento (por empenho): bloqueio esperado confirmado.');
                $this->line('Motivo: ' . $exception->getMessage());
                return self::SUCCESS;
            }
            $this->warn('Regra de pagamento: ' . $exception->getMessage());
        }

        if (!empty($codigoOrdem)) {
            try {
                $service->assertPodeRegistrarPagamentoPorOrdem((int) $codigoOrdem);
                if ($expectPaymentBlock) {
                    $this->error('Regra de pagamento (por ordem): era esperado bloqueio, mas a validacao passou.');
                    return self::FAILURE;
                }
                $this->info('Regra de pagamento (por ordem): OK');
            } catch (LogicException $exception) {
                if ($expectPaymentBlock) {
                    $this->info('Regra de pagamento (por ordem): bloqueio esperado confirmado.');
                    $this->line('Motivo: ' . $exception->getMessage());
                    return self::SUCCESS;
                }
                $this->warn('Regra de pagamento (por ordem): ' . $exception->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
