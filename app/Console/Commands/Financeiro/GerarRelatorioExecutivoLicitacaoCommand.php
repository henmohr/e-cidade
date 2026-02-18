<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\RelatorioExecutivoLicitacaoService;
use Illuminate\Console\Command;

class GerarRelatorioExecutivoLicitacaoCommand extends Command
{
    protected $signature = 'financeiro:gerar-relatorio-executivo-licitacao
                            {--saida=docs/sprint16_relatorio_executivo : Diretorio de saida}
                            {--s11=docs/sprint11_cobertura_modulos/cobertura_modulos_financeiros.json : Arquivo JSON da Sprint 11}
                            {--s12=docs/sprint12_rastreabilidade_funcional/rastreabilidade_funcional.json : Arquivo JSON da Sprint 12}
                            {--s14=docs/sprint14_homologacao_externa/checklist_homologacao_externa.json : Arquivo JSON da Sprint 14}
                            {--s15=docs/sprint15_gate_entrega/gate_entrega_licitacao.json : Arquivo JSON da Sprint 15}';

    protected $description = 'Gera relatorio executivo consolidado de prontidao para licitacao';

    public function handle(RelatorioExecutivoLicitacaoService $service): int
    {
        $saida = (string) $this->option('saida');
        if (!is_dir($saida)) {
            mkdir($saida, 0777, true);
        }

        $resumo = $service->gerarResumo([
            'sprint11' => (string) $this->option('s11'),
            'sprint12' => (string) $this->option('s12'),
            'sprint14' => (string) $this->option('s14'),
            'sprint15' => (string) $this->option('s15'),
        ]);

        $arquivoJson = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'relatorio_executivo_licitacao.json';
        $arquivoMd = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'relatorio_executivo_licitacao.md';

        file_put_contents($arquivoJson, json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($arquivoMd, $service->gerarMarkdown($resumo));

        $this->line('Relatorio executivo da licitacao gerado');
        $this->line('JSON: ' . $arquivoJson);
        $this->line('Markdown: ' . $arquivoMd);
        $this->line('Decisao: ' . (string) ($resumo['decisao'] ?? 'segurar_envio'));

        return self::SUCCESS;
    }
}
