<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpsSlaReport extends Command
{
    protected $signature = 'ops:sla-report
                            {--hours=24 : Janela em horas}
                            {--path= : Caminho do log de amostras}';

    protected $description = 'Calcula disponibilidade (SLA) a partir das amostras de health';

    public function handle(): int
    {
        $path = (string) ($this->option('path') ?: config('observability.sample_log_path'));
        $hours = max(1, (int) $this->option('hours'));
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

        $this->table(
            ['Janela (h)', 'Amostras', 'Disponiveis', 'SLA %', 'Meta %', 'Atingiu Meta'],
            [[
                $hours,
                $total,
                $ok,
                number_format($availability, 4, '.', ''),
                number_format($target, 4, '.', ''),
                $availability >= $target ? 'sim' : 'nao',
            ]]
        );

        return self::SUCCESS;
    }
}
