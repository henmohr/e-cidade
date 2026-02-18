<?php

namespace App\Services\Financeiro\Credor;

use App\Repositories\Financeiro\Credor\CredorRepository;
use App\Repositories\Financeiro\Credor\CredorRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ValidacaoCredorService
{
    private CredorRepositoryInterface $repository;
    private LoggerInterface $logger;

    public function __construct(
        ?CredorRepositoryInterface $repository = null,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository ?? new CredorRepository();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, mixed>
     */
    public function validarPorCgm(int $numcgm): array
    {
        $credor = $this->repository->obterCredorPorCgm($numcgm);
        if ($credor === null) {
            return [
                'numcgm' => $numcgm,
                'status' => 'NAO_ENCONTRADO',
                'apto' => false,
                'pendencias' => ['Credor/CGM nao encontrado.'],
                'dados' => null,
            ];
        }

        $pendencias = [];
        $documento = $this->somenteNumeros((string) ($credor['z01_cgccpf'] ?? ''));

        if ($documento === '') {
            $pendencias[] = 'CPF/CNPJ nao informado.';
        } elseif (!$this->isCpfOuCnpjValido($documento)) {
            $pendencias[] = 'CPF/CNPJ invalido.';
        }

        if (empty($credor['fornecedor_cgm'])) {
            $pendencias[] = 'Credor sem cadastro de fornecedor (pcforne).';
        }

        if (($credor['pc60_bloqueado'] ?? null) === 't') {
            $pendencias[] = 'Fornecedor bloqueado para contratacao.';
        }

        if (strlen($documento) === 14) {
            if (empty($credor['pc60_inscriestadual'])) {
                $pendencias[] = 'Pendencia documental: inscricao estadual nao informada.';
            }
            if (empty($credor['pc60_orgaoreg']) || empty($credor['pc60_numeroregistro'])) {
                $pendencias[] = 'Pendencia documental: orgao/numero de registro nao informados.';
            }
        }

        $apto = count($pendencias) === 0;
        $status = $apto ? 'APTO' : 'PENDENTE_DOCUMENTAL';

        $resultado = [
            'numcgm' => (int) $credor['z01_numcgm'],
            'status' => $status,
            'apto' => $apto,
            'pendencias' => $pendencias,
            'dados' => [
                'nome' => (string) ($credor['z01_nome'] ?? ''),
                'documento' => $documento,
                'email' => (string) ($credor['z01_email'] ?? ''),
            ],
        ];

        if ($apto) {
            $this->logger->info('Credor validado sem pendencias documentais.', $resultado);
        } else {
            $this->logger->warning('Credor com pendencias documentais.', $resultado);
        }

        return $resultado;
    }

    private function somenteNumeros(string $valor): string
    {
        return preg_replace('/\D+/', '', $valor) ?? '';
    }

    private function isCpfOuCnpjValido(string $documento): bool
    {
        if (strlen($documento) === 11) {
            return $this->isCpfValido($documento);
        }

        if (strlen($documento) === 14) {
            return $this->isCnpjValido($documento);
        }

        return false;
    }

    private function isCpfValido(string $cpf): bool
    {
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] !== $d) {
                return false;
            }
        }

        return true;
    }

    private function isCnpjValido(string $cnpj): bool
    {
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $peso1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $peso2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $digito1 = $this->calcularDigitoCnpj($cnpj, $peso1, 12);
        $digito2 = $this->calcularDigitoCnpj($cnpj, $peso2, 13);

        return ((int) $cnpj[12] === $digito1) && ((int) $cnpj[13] === $digito2);
    }

    /**
     * @param array<int, int> $pesos
     */
    private function calcularDigitoCnpj(string $cnpj, array $pesos, int $tamanho): int
    {
        $soma = 0;
        for ($i = 0; $i < $tamanho; $i++) {
            $soma += (int) $cnpj[$i] * $pesos[$i];
        }

        $resto = $soma % 11;
        return $resto < 2 ? 0 : 11 - $resto;
    }
}

