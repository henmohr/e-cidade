<?php

namespace App\Console\Commands\Financeiro;

use App\Services\Financeiro\Licitacao\CoberturaLicitacaoService;
use Illuminate\Console\Command;

class RelatorioCoberturaLicitacaoCommand extends Command
{
    protected $signature = 'financeiro:relatorio-cobertura-licitacao
                            {--arquivo=docs/sprint9_matriz_status_licitacao.yml : Arquivo yaml da matriz de status}
                            {--saida= : Arquivo markdown de saida para o relatorio}';

    protected $description = 'Gera relatorio consolidado de cobertura da licitacao por modulo/sistema';

    public function handle(CoberturaLicitacaoService $service): int
    {
        $arquivo = (string) $this->option('arquivo');
        $saida = (string) ($this->option('saida') ?: 'docs/sprint9_relatorio_cobertura_licitacao.md');

        $resumo = $service->gerarResumo($arquivo);
        $markdown = $service->gerarMarkdown($resumo);

        file_put_contents($saida, $markdown);

        $this->line('Relatorio de cobertura gerado');
        $this->line('Arquivo base: ' . $arquivo);
        $this->line('Saida: ' . $saida);
        $this->line('Atingidos: ' . $resumo['totais']['atingido']);
        $this->line('Parciais: ' . $resumo['totais']['parcial']);
        $this->line('Pendentes: ' . $resumo['totais']['pendente']);
        $this->line('Percentual atingido: ' . number_format((float) $resumo['percentual_atingido'], 2, '.', '') . '%');
        $this->line('Status recomendado: ' . $resumo['status_recomendado']);

        return self::SUCCESS;
    }
}
