<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\CoberturaModulosFinanceirosService;
use Illuminate\Console\Command;

class RelatorioCoberturaModulosFinanceirosCommand extends Command
{
    protected $signature = 'financeiro:relatorio-cobertura-modulos
                            {--saida=docs/sprint11_cobertura_modulos : Diretorio de saida do relatorio}';

    protected $description = 'Gera relatorio de cobertura dos modulos financeiros (M1 a M5) para a Sprint 11';

    public function handle(CoberturaModulosFinanceirosService $service): int
    {
        $saida = (string) $this->option('saida');
        if (!is_dir($saida)) {
            mkdir($saida, 0777, true);
        }

        $resumo = $service->gerarResumo();
        $markdown = $service->gerarMarkdown($resumo);

        $arquivoJson = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cobertura_modulos_financeiros.json';
        $arquivoMd = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cobertura_modulos_financeiros.md';

        file_put_contents($arquivoJson, json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($arquivoMd, $markdown);

        $this->line('Relatorio de cobertura dos modulos financeiros gerado');
        $this->line('JSON: ' . $arquivoJson);
        $this->line('Markdown: ' . $arquivoMd);
        $this->line('Status recomendado: ' . (string) ($resumo['status_recomendado'] ?? 'plano_de_acao_obrigatorio'));

        return self::SUCCESS;
    }
}
