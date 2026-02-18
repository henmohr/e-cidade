<?php

namespace App\Services\Financeiro\Integracao;

class HomologacaoAnexosService
{
    /**
     * @return array<int, string>
     */
    public function listarAnexosObrigatorios(): array
    {
        return [
            'siconfi_homologacao_assinada.md',
            'tce_uf_homologacao_assinada.md',
            'portal_transparencia_homologacao_assinada.md',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validarDiretorio(string $diretorioBase): array
    {
        $obrigatorios = $this->listarAnexosObrigatorios();
        $presentes = [];
        $ausentes = [];
        $vazios = [];

        foreach ($obrigatorios as $arquivo) {
            $caminho = rtrim($diretorioBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $arquivo;

            if (!is_file($caminho)) {
                $ausentes[] = $arquivo;
                continue;
            }

            $tamanho = filesize($caminho);
            if ($tamanho === false || $tamanho <= 0) {
                $vazios[] = $arquivo;
                continue;
            }

            $presentes[] = $arquivo;
        }

        return [
            'diretorio' => $diretorioBase,
            'obrigatorios' => $obrigatorios,
            'presentes' => $presentes,
            'ausentes' => $ausentes,
            'vazios' => $vazios,
            'status' => (count($ausentes) === 0 && count($vazios) === 0) ? 'ok' : 'pendente',
        ];
    }
}
