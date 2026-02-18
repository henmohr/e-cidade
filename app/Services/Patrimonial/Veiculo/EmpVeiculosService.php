<?php

namespace App\Services\Patrimonial\Veiculo;

use App\Models\EmpVeiculos;
use App\Repositories\Financeiro\EmpEmpenhoRepository;
use App\Repositories\Patrimonial\EmpVeiculosRepository;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;

class EmpVeiculosService
{
    private EmpVeiculosRepository $empVeiculosRepository;
    private EmpEmpenhoRepository $empEmpenhoRepository;
    private CicloDespesaService $cicloDespesaService;

    public function __construct(
        ?EmpVeiculosRepository $empVeiculosRepository = null,
        ?EmpEmpenhoRepository $empEmpenhoRepository = null,
        ?CicloDespesaService $cicloDespesaService = null
    )
    {
        $this->empVeiculosRepository = $empVeiculosRepository ?? new EmpVeiculosRepository();
        $this->empEmpenhoRepository = $empEmpenhoRepository ?? new EmpEmpenhoRepository();
        $this->cicloDespesaService = $cicloDespesaService ?? new CicloDespesaService();
    }

    /**
     * @param array $dados
     * @return void
     */
    public function insert(array $dados): ?EmpVeiculos
    {
        $empenho = [];
        $empenho['si05_numemp'] = $this->getCodigoEmpenho($dados['empenho']);
        $this->cicloDespesaService->assertPodeLiquidar((int) $empenho['si05_numemp']);
        $empenho['si05_atestado'] = true;
        $empenho['si05_codabast'] = $dados['codigoAbastecimento'];
        $empenho['si05_item_empenho'] = false;

        return $this->empVeiculosRepository->insert($empenho);
    }

    /**
     * @param string $empenho
     * @return integer
     */
    private function getCodigoEmpenho(string $empenho): int
    {
        $numeroEmpArray = explode('/', $empenho);

        $codigoEmpenho = $this->empEmpenhoRepository->getCodigoEmpenho($numeroEmpArray[0], $numeroEmpArray[1]);

        return $codigoEmpenho;
    }
}
