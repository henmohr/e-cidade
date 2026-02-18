<?php

namespace App\Tests\Unit\Services\Patrimonial\Veiculo;

use App\Repositories\Financeiro\EmpEmpenhoRepository;
use App\Repositories\Patrimonial\EmpVeiculosRepository;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use App\Services\Patrimonial\Veiculo\EmpVeiculosService;
use PHPUnit\Framework\TestCase;

class EmpVeiculosServiceTest extends TestCase
{
    public function testValidaCicloAntesDeInserirAtestoVeicular(): void
    {
        $empVeiculosRepository = $this->createMock(EmpVeiculosRepository::class);
        $empEmpenhoRepository = $this->createMock(EmpEmpenhoRepository::class);
        $cicloDespesaService = $this->createMock(CicloDespesaService::class);

        $empEmpenhoRepository
            ->expects($this->once())
            ->method('getCodigoEmpenho')
            ->with('12345', '2026')
            ->willReturn(9001);

        $cicloDespesaService
            ->expects($this->once())
            ->method('assertPodeLiquidar')
            ->with(9001);

        $empVeiculosRepository
            ->expects($this->once())
            ->method('insert')
            ->with($this->callback(static function (array $dados): bool {
                return $dados['si05_numemp'] === 9001
                    && $dados['si05_atestado'] === true
                    && $dados['si05_codabast'] === 77
                    && $dados['si05_item_empenho'] === false;
            }))
            ->willReturn(null);

        $service = new EmpVeiculosService(
            $empVeiculosRepository,
            $empEmpenhoRepository,
            $cicloDespesaService
        );

        $service->insert([
            'empenho' => '12345/2026',
            'codigoAbastecimento' => 77,
        ]);
    }
}
