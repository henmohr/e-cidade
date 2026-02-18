<?php

namespace App\Services\Financeiro\Credor;

use App\Repositories\Financeiro\Credor\FluxoDespesaCredorRepository;
use App\Repositories\Financeiro\Credor\FluxoDespesaCredorRepositoryInterface;
use App\Services\Financeiro\ExecucaoOrcamentaria\CicloDespesaService;
use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class FluxoDespesaCredorService
{
    private FluxoDespesaCredorRepositoryInterface $repository;
    private ValidacaoCredorService $validacaoCredorService;
    private CicloDespesaService $cicloDespesaService;
    private LoggerInterface $logger;

    public function __construct(
        ?FluxoDespesaCredorRepositoryInterface $repository = null,
        ?ValidacaoCredorService $validacaoCredorService = null,
        ?CicloDespesaService $cicloDespesaService = null,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository ?? new FluxoDespesaCredorRepository();
        $this->validacaoCredorService = $validacaoCredorService ?? new ValidacaoCredorService();
        $this->cicloDespesaService = $cicloDespesaService ?? new CicloDespesaService();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, mixed>
     */
    public function validarFluxoMinimo(int $numcgm, int $numeroEmpenho): array
    {
        $validacaoCredor = $this->validacaoCredorService->validarPorCgm($numcgm);
        if (!($validacaoCredor['apto'] ?? false)) {
            $resultado = [
                'status' => 'CREDOR_PENDENTE',
                'numcgm' => $numcgm,
                'empenho' => $numeroEmpenho,
                'etapas' => [
                    'credor' => false,
                    'empenho' => false,
                    'atesto' => false,
                    'pagamento' => false,
                ],
                'pendencias' => $validacaoCredor['pendencias'] ?? ['Credor pendente.'],
            ];

            $this->logger->warning('Fluxo de despesa bloqueado na etapa de credor.', $resultado);
            return $resultado;
        }

        if (!$this->repository->hasEmpenhoDoCredor($numeroEmpenho, $numcgm)) {
            $resultado = [
                'status' => 'PENDENTE_EMPENHO',
                'numcgm' => $numcgm,
                'empenho' => $numeroEmpenho,
                'etapas' => [
                    'credor' => true,
                    'empenho' => false,
                    'atesto' => false,
                    'pagamento' => false,
                ],
                'pendencias' => ['Empenho nao localizado para o credor informado.'],
            ];

            $this->logger->warning('Fluxo de despesa bloqueado por ausencia de empenho.', $resultado);
            return $resultado;
        }

        $this->cicloDespesaService->assertPodeLiquidar($numeroEmpenho);

        if (!$this->repository->hasAtestoDoEmpenho($numeroEmpenho)) {
            $resultado = [
                'status' => 'PENDENTE_ATESTO',
                'numcgm' => $numcgm,
                'empenho' => $numeroEmpenho,
                'etapas' => [
                    'credor' => true,
                    'empenho' => true,
                    'atesto' => false,
                    'pagamento' => false,
                ],
                'pendencias' => ['Atesto/nota fiscal ainda nao registrado para o empenho.'],
            ];

            $this->logger->warning('Fluxo de despesa bloqueado por ausencia de atesto.', $resultado);
            return $resultado;
        }

        try {
            $this->cicloDespesaService->assertPodePagar($numeroEmpenho);
        } catch (LogicException $exception) {
            $resultado = [
                'status' => 'PENDENTE_PAGAMENTO',
                'numcgm' => $numcgm,
                'empenho' => $numeroEmpenho,
                'etapas' => [
                    'credor' => true,
                    'empenho' => true,
                    'atesto' => true,
                    'pagamento' => false,
                ],
                'pendencias' => [$exception->getMessage()],
            ];

            $this->logger->warning('Fluxo de despesa bloqueado na etapa de pagamento.', $resultado);
            return $resultado;
        }

        $pagamentoEfetivado = $this->repository->hasPagamentoDoEmpenho($numeroEmpenho);
        $resultado = [
            'status' => $pagamentoEfetivado ? 'FLUXO_COMPLETO' : 'PENDENTE_PAGAMENTO',
            'numcgm' => $numcgm,
            'empenho' => $numeroEmpenho,
            'etapas' => [
                'credor' => true,
                'empenho' => true,
                'atesto' => true,
                'pagamento' => $pagamentoEfetivado,
            ],
            'pendencias' => $pagamentoEfetivado ? [] : ['Pagamento ainda nao efetivado.'],
        ];

        if ($pagamentoEfetivado) {
            $this->logger->info('Fluxo minimo credor->empenho->atesto->pagamento validado.', $resultado);
        } else {
            $this->logger->warning('Fluxo valido ate atesto, aguardando pagamento.', $resultado);
        }

        return $resultado;
    }
}

