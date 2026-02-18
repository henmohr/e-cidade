<?php

namespace App\Services\Financeiro\Tesouraria;

use DateTimeImmutable;

class DashboardTesourariaService
{
    private FluxoCaixaService $fluxoCaixaService;
    private RestosAPagarService $restosAPagarService;

    public function __construct(
        ?FluxoCaixaService $fluxoCaixaService = null,
        ?RestosAPagarService $restosAPagarService = null
    ) {
        $this->fluxoCaixaService = $fluxoCaixaService ?? new FluxoCaixaService();
        $this->restosAPagarService = $restosAPagarService ?? new RestosAPagarService();
    }

    /**
     * @return array<string, mixed>
     */
    public function gerar(int $conta, int $reduz, ?int $ano = null, ?string $dataBase = null): array
    {
        $projecao = $this->fluxoCaixaService->projetar7Dias($conta, $reduz, $dataBase);
        $restos = $this->restosAPagarService->obterResumo($ano);

        $alertas = [];
        if ((float) $projecao['menor_saldo_previsto'] < 0) {
            $alertas[] = 'Saldo projetado negativo na janela de 7 dias.';
        }
        if ((float) $restos['restos_processados'] > 0) {
            $alertas[] = 'Existem restos a pagar processados pendentes.';
        }
        if ((float) $restos['restos_nao_processados'] > 0) {
            $alertas[] = 'Existem restos a pagar nao processados pendentes.';
        }

        return [
            'atualizado_em' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'saldo_atual' => $projecao['saldo_inicial'],
            'projecao_7_dias' => $projecao,
            'restos_a_pagar' => $restos,
            'alertas' => $alertas,
        ];
    }
}

