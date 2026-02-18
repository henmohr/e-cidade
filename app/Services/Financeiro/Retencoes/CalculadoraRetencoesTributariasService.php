<?php

namespace App\Services\Financeiro\Retencoes;

use LogicException;

class CalculadoraRetencoesTributariasService
{
    /**
     * @param array<string, float> $aliquotas
     * @param array<string, float> $retencoesAdicionais
     * @return array<string, mixed>
     */
    public function calcular(
        float $valorBruto,
        array $aliquotas = [],
        array $retencoesAdicionais = []
    ): array {
        if ($valorBruto <= 0) {
            throw new LogicException('Valor bruto deve ser maior que zero para calcular retencoes.');
        }

        $aliquotasPadrao = [
            'irrf' => 1.50,
            'iss' => 2.00,
            'inss' => 11.00,
        ];

        $aliquotasAplicadas = array_merge($aliquotasPadrao, $aliquotas);
        foreach ($aliquotasAplicadas as $tributo => $aliquota) {
            if ($aliquota < 0) {
                throw new LogicException("Aliquota invalida para {$tributo}: nao pode ser negativa.");
            }
        }

        $retencoes = [];
        $totalRetencoes = 0.0;

        foreach ($aliquotasAplicadas as $tributo => $aliquota) {
            $valorRetencao = round(($valorBruto * $aliquota) / 100, 2);
            $retencoes[$tributo] = [
                'aliquota' => $aliquota,
                'valor' => $valorRetencao,
            ];
            $totalRetencoes += $valorRetencao;
        }

        foreach ($retencoesAdicionais as $nome => $valor) {
            if ($valor < 0) {
                throw new LogicException("Retencao adicional invalida para {$nome}: valor nao pode ser negativo.");
            }
            $valorArredondado = round($valor, 2);
            $retencoes[$nome] = [
                'aliquota' => null,
                'valor' => $valorArredondado,
            ];
            $totalRetencoes += $valorArredondado;
        }

        $totalRetencoes = round($totalRetencoes, 2);
        $valorLiquido = round($valorBruto - $totalRetencoes, 2);

        return [
            'valor_bruto' => round($valorBruto, 2),
            'retencoes' => $retencoes,
            'total_retencoes' => $totalRetencoes,
            'valor_liquido' => $valorLiquido,
        ];
    }
}

