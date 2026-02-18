<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\RastreabilidadeFuncionalService;
use Illuminate\Console\Command;

class GerarRastreabilidadeFuncionalCommand extends Command
{
    protected $signature = 'financeiro:gerar-rastreabilidade-funcional
                            {--saida=docs/sprint12_rastreabilidade_funcional : Diretorio de saida dos artefatos}';

    protected $description = 'Gera rastreabilidade funcional por cenario para os modulos M1-M5';

    public function handle(RastreabilidadeFuncionalService $service): int
    {
        $saida = (string) $this->option('saida');
        if (!is_dir($saida)) {
            mkdir($saida, 0777, true);
        }

        $resumo = $service->gerarResumo();
        $markdown = $service->gerarMarkdown($resumo);

        $arquivoJson = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'rastreabilidade_funcional.json';
        $arquivoMd = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'rastreabilidade_funcional.md';

        file_put_contents($arquivoJson, json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($arquivoMd, $markdown);

        $this->line('Rastreabilidade funcional gerada');
        $this->line('JSON: ' . $arquivoJson);
        $this->line('Markdown: ' . $arquivoMd);
        $this->line('Status recomendado: ' . (string) ($resumo['status_recomendado'] ?? 'homologacao_assistida'));

        return self::SUCCESS;
    }
}
