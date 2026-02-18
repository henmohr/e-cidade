<?php

namespace App\Services\Financeiro\Licitacao;

class JsonArquivoLoaderService
{
    /**
     * @param array<string, string> $arquivos
     * @return array{dados: array<string, array<string, mixed>>, erros: array<int, string>}
     */
    public function carregarMapa(array $arquivos): array
    {
        $dados = [];
        $erros = [];

        foreach ($arquivos as $chave => $arquivo) {
            $json = $this->carregar($arquivo);
            if ($json === null) {
                $erros[] = 'Arquivo ausente ou invalido: ' . $arquivo;
                continue;
            }

            $dados[$chave] = $json;
        }

        return [
            'dados' => $dados,
            'erros' => $erros,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function carregar(string $arquivo): ?array
    {
        if ($arquivo === '' || !is_file($arquivo)) {
            return null;
        }

        $conteudo = (string) file_get_contents($arquivo);
        $dados = json_decode($conteudo, true);

        return is_array($dados) ? $dados : null;
    }
}
