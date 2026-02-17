<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpsSlaReport extends Command
{
    protected $signature = 'ops:sla-report
                            {--hours=24 : Janela em horas}
                            {--path= : Caminho do log de amostras}
                            {--format=table : Formato de saida (table|json)}
                            {--append-log : Grava resumo no log de relatorios}';

    protected $description = 'Calcula disponibilidade (SLA) a partir das amostras de health';

    public function handle(): int
    {
        $path = (string) ($this->option('path') ?: config('observability.sample_log_path'));
        $hours = max(1, (int) $this->option('hours'));
        $format = (string) $this->option('format');
        $cutoff = now()->subHours($hours)->getTimestamp();

        if (!is_file($path)) {
            $this->error('Arquivo de amostras nao encontrado: ' . $path);
            return self::FAILURE;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $total = 0;
        $ok = 0;

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if (!is_array($data)) {
                continue;
            }

            $ts = isset($data['timestamp']) ? strtotime((string) $data['timestamp']) : 0;
            if ($ts <= 0 || $ts < $cutoff) {
                continue;
            }

            $total++;
            if (($data['live']['ok'] ?? false) && ($data['ready']['ok'] ?? false)) {
                $ok++;
            }
        }

        if ($total === 0) {
            $this->warn('Sem amostras na janela analisada.');
            return self::SUCCESS;
        }

        $availability = round(($ok / $total) * 100, 4);
        $target = (float) config('observability.sla_target_percent', 99.9);
        $achieved = $availability >= $target;

        $payload = [
            'timestamp' => now()->toIso8601String(),
            'window_hours' => $hours,
            'samples_total' => $total,
            'samples_ok' => $ok,
            'sla_percent' => (float) number_format($availability, 4, '.', ''),
            'target_percent' => (float) number_format($target, 4, '.', ''),
            'achieved' => $achieved,
        ];

        if ($format === 'json') {
            $this->line(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(
                ['Janela (h)', 'Amostras', 'Disponiveis', 'SLA %', 'Meta %', 'Atingiu Meta'],
                [[
                    $hours,
                    $total,
                    $ok,
                    number_format($availability, 4, '.', ''),
                    number_format($target, 4, '.', ''),
                    $achieved ? 'sim' : 'nao',
                ]]
            );
        }

        if ($this->option('append-log')) {
            $reportPath = (string) config('observability.sla_report_log_path');
            @mkdir(dirname($reportPath), 0775, true);
            file_put_contents($reportPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
            $this->info('Resumo gravado em: ' . $reportPath);
        }

        return self::SUCCESS;
    }
}
