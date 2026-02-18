<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Integracao\ChecklistHomologacaoExternaService;
use Illuminate\Console\Command;

class ChecklistHomologacaoExternaCommand extends Command
{
    protected $signature = 'financeiro:checklist-homologacao-externa
                            {--anexos=docs/anexos_homologacao_assinados : Diretorio de anexos assinados}
                            {--saida=docs/sprint14_homologacao_externa : Diretorio de saida}
                            {--limite=200 : Limite de registros por status}
                            {--sistemas= : Sistemas separados por virgula (SICONFI,TCE_PR,PORTAL_TRANSPARENCIA)}
                            {--protocolos= : Arquivo JSON ou YAML com totais oficiais por sistema}
                            {--offline : Executa sem consultar repositorio, usando apenas arquivo de protocolos quando informado}';

    protected $description = 'Gera checklist consolidado de homologacao externa por sistema';

    public function handle(ChecklistHomologacaoExternaService $service): int
    {
        $sistemasOption = (string) $this->option('sistemas');
        $sistemas = $sistemasOption !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $sistemasOption))))
            : null;

        $saida = (string) $this->option('saida');
        if (!is_dir($saida)) {
            mkdir($saida, 0777, true);
        }

        $resumo = $service->gerarResumo(
            $sistemas,
            (string) $this->option('anexos'),
            (int) $this->option('limite'),
            $this->option('protocolos') ? (string) $this->option('protocolos') : null,
            (bool) $this->option('offline')
        );

        $arquivoJson = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'checklist_homologacao_externa.json';
        $arquivoMd = rtrim($saida, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'checklist_homologacao_externa.md';

        file_put_contents($arquivoJson, json_encode($resumo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($arquivoMd, $service->gerarMarkdown($resumo));

        $this->line('Checklist de homologacao externa gerado');
        $this->line('JSON: ' . $arquivoJson);
        $this->line('Markdown: ' . $arquivoMd);
        $this->line('Status final: ' . (string) ($resumo['status_final'] ?? 'plano_de_acao_obrigatorio'));

        return self::SUCCESS;
    }
}
