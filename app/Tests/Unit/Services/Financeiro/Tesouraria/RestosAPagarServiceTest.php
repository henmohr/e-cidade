<?php

namespace App\Tests\Unit\Services\Financeiro\Tesouraria;

use App\Repositories\Financeiro\Tesouraria\RestosAPagarRepositoryInterface;
use App\Services\Financeiro\Tesouraria\RestosAPagarService;
use PHPUnit\Framework\TestCase;

class RestosAPagarServiceTest extends TestCase
{
    public function testConsolidaTotaisDeRestosAPagar(): void
    {
        $repository = $this->createMock(RestosAPagarRepositoryInterface::class);
        $repository->method('obterTotais')->willReturn([
            'restos_processados' => 1200.50,
            'restos_nao_processados' => 300.25,
        ]);

        $service = new RestosAPagarService($repository);
        $resumo = $service->obterResumo(2026);

        $this->assertSame(2026, $resumo['ano']);
        $this->assertSame(1200.50, $resumo['restos_processados']);
        $this->assertSame(300.25, $resumo['restos_nao_processados']);
        $this->assertSame(1500.75, $resumo['restos_total']);
    }
}

