<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Tesouraria\ConciliacaoBancariaService;
use Illuminate\Console\Command;

class ConciliarContaBancariaCommand extends Command
{
    protected $signature = 'financeiro:conciliar-conta-bancaria
                            {conta : Codigo da conta bancaria interna (k13_conta)}
                            {reduz : Codigo reduzido da conta plano (k13_reduz)}
                            {saldo-extrato : Saldo do extrato bancario na data de referencia}
                            {--data= : Data de referencia no formato YYYY-MM-DD}
                            {--tolerancia=0.01 : Tolerancia permitida para diferenca de conciliacao}';

    protected $description = 'Executa conciliacao bancaria diaria e informa status de pendencias';

    public function handle(ConciliacaoBancariaService $service): int
    {
        $conta = (int) $this->argument('conta');
        $reduz = (int) $this->argument('reduz');
        $saldoExtrato = (float) $this->argument('saldo-extrato');
        $data = $this->option('data');
        $tolerancia = (float) $this->option('tolerancia');

        $resultado = $service->conciliar($conta, $reduz, $saldoExtrato, $data ?: null, $tolerancia);

        $this->line('Status da conciliacao: ' . $resultado['status']);
        $this->line('Conta/Reduz: ' . $resultado['conta'] . '/' . $resultado['reduz']);
        $this->line('Data referencia: ' . ($resultado['data_referencia'] ?? 'nao informada'));
        $this->line('Saldo sistema: ' . ($resultado['saldo_sistema'] ?? 'nao encontrado'));
        $this->line('Saldo extrato: ' . $resultado['saldo_extrato']);
        $this->line('Diferenca: ' . ($resultado['diferenca'] ?? 'nao calculada'));

        return ($resultado['conciliado'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}

