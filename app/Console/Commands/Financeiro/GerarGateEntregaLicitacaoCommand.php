<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\GateEntregaLicitacaoService;
use Illuminate\Console\Command;

class GerarGateEntregaLicitacaoCommand extends Command
{
    protected $signature = 'financeiro:gerar-gate-entrega-licitacao
                            {--saida=docs/sprint15_gate_entrega : Diretorio de saida}
                            {--s11=docs/sprint11_cobertura_modulos/cobertura_modulos_financeiros.json : Arquivo JSON da Sprint 11}
                            {--s12=docs/sprint12_rastreabilidade_funcional/rastreabilidade_funcional.json : Arquivo JSON da Sprint 12}
                            {--s14=docs/sprint14_homologacao_externa/checklist_homologacao_externa.json : Arquivo JSON da Sprint 14}
                            {--banca=docs/pacote_final_banca/manifesto_final_banca.json : Manifesto final de banca}';

    protected $description = 'Gera gate consolidado de entrega da licitacao com status bloqueado/apto_para_entrega';

    public function handle(GateEntregaLicitacaoService $service): int
    {
        $saida = (string) $this->option('saida');
        if (!is_dir($saida)) {
            mkdir($saida, 0777, true);
        }

        $resumo = $service->gerarResumo([
            'sprint11' => (string) $this->option('s11'),
            'sprint12' => (string) $this->option('s12'),
            'sprint14' => (string) $this->option('s14'),
            'banca' => (string) $this->option('banca'),
        ]);

        $arquivoJson = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'gate_entrega_licitacao.json';
        $arquivoMd = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'gate_entrega_licitacao.md';

        file_put_contents($arquivoJson, json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($arquivoMd, $service->gerarMarkdown($resumo));

        $this->line('Gate de entrega da licitacao gerado');
        $this->line('JSON: ' . $arquivoJson);
        $this->line('Markdown: ' . $arquivoMd);
        $this->line('Status final: ' . (string) ($resumo['status_final'] ?? 'bloqueado'));

        return self::SUCCESS;
    }
}
