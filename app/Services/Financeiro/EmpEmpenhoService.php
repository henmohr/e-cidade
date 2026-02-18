<?php
namespace App\Services\Financeiro;

use App\Repositories\Financeiro\EmpEmpenhoRepository;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;

class EmpEmpenhoService
{
    private EmpEmpenhoRepository $empempenhoRepository;
    private CicloDespesaService $cicloDespesaService;

    public function __construct(
        ?EmpEmpenhoRepository $empempenhoRepository = null,
        ?CicloDespesaService $cicloDespesaService = null
    )
    {
        $this->empempenhoRepository = $empempenhoRepository ?? new EmpEmpenhoRepository();
        $this->cicloDespesaService = $cicloDespesaService ?? new CicloDespesaService();
    }

    /**
     * @param float $valor
     * @param string $empenho
     * @return void
     */
    public function alterarSaldoDisponivel(float $valor, string $empenho): void
    {
        $empenho = explode("/", $empenho);
        $empenhoData = $this->empempenhoRepository->getEmpenho($empenho[0], $empenho[1]);

        if (empty($empenhoData)) {
            throw new \LogicException("Empenho nao encontrado para alteracao de saldo.");
        }

        // Regra do ciclo: exige fixacao e disponibilidade da dotacao para incremento de saldo.
        if ($valor > 0) {
            $this->cicloDespesaService->assertPodeEmpenhar(
                (int) $empenhoData->e60_anousu,
                (int) $empenhoData->e60_coddot,
                (float) $valor
            );
        } else {
            $this->cicloDespesaService->assertPodeEmpenhar(
                (int) $empenhoData->e60_anousu,
                (int) $empenhoData->e60_coddot
            );
        }

        $saldoUtilizado = (float)$empenhoData->e60_vlrutilizado + $valor;
        $dadosUpdate = ['e60_vlrutilizado' => $saldoUtilizado];
        $result = $this->empempenhoRepository->atualizarEmpenho((int)$empenhoData->e60_numemp, $dadosUpdate);

        if (!$result) {
            throw new \LogicException("Nao foi possivel atualizar empenho");
        }
    }
}
