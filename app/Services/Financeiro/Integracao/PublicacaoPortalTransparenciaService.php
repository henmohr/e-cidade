<?php

namespace App\Services\Financeiro\Integracao;

use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepository;
use App\Repositories\Financeiro\Integracao\IntegracaoGovernamentalRepositoryInterface;
use App\Repositories\Financeiro\Integracao\PortalTransparenciaPublicacaoRepository;
use App\Repositories\Financeiro\Integracao\PortalTransparenciaPublicacaoRepositoryInterface;

class PublicacaoPortalTransparenciaService
{
    private PortalTransparenciaPublicacaoRepositoryInterface $publicacaoRepository;
    private IntegracaoGovernamentalRepositoryInterface $integracaoRepository;

    public function __construct(
        ?PortalTransparenciaPublicacaoRepositoryInterface $publicacaoRepository = null,
        ?IntegracaoGovernamentalRepositoryInterface $integracaoRepository = null
    ) {
        $this->publicacaoRepository = $publicacaoRepository ?? new PortalTransparenciaPublicacaoRepository();
        $this->integracaoRepository = $integracaoRepository ?? new IntegracaoGovernamentalRepository();
    }

    /**
     * @return array<string, mixed>
     */
    public function publicar(?string $dataReferencia = null): array
    {
        $referencia = $dataReferencia ?: date('Y-m-d');
        $receitas = $this->publicacaoRepository->obterDadosReceitas($referencia);
        $despesas = $this->publicacaoRepository->obterDadosDespesas($referencia);
        $contratos = $this->publicacaoRepository->obterDadosContratos($referencia);

        $resumo = [
            'receitas' => $this->consolidarResumo($receitas),
            'despesas' => $this->consolidarResumo($despesas),
            'contratos' => $this->consolidarResumo($contratos),
        ];

        $codigoPublicacao = $this->integracaoRepository->criarRegistro(
            'PORTAL_TRANSPARENCIA',
            $referencia,
            IntegracaoGovernamentalStatusService::STATUS_ACEITO,
            [
                'data_referencia' => $referencia,
                'resumo' => $resumo,
            ]
        );

        return [
            'codigo_publicacao' => $codigoPublicacao,
            'data_referencia' => $referencia,
            'resumo' => $resumo,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $dados
     * @return array<string, float|int>
     */
    private function consolidarResumo(array $dados): array
    {
        $totalValor = 0.0;
        $totalRegistros = 0;

        foreach ($dados as $linha) {
            $totalValor += (float) ($linha['valor_total'] ?? 0);
            $totalRegistros += (int) ($linha['total_registros'] ?? 0);
        }

        return [
            'valor_total' => round($totalValor, 2),
            'total_registros' => $totalRegistros,
        ];
    }
}
