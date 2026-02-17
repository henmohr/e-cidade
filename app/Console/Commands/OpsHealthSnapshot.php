<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class OpsHealthSnapshot extends Command
{
    protected $signature = 'ops:health-snapshot
                            {--base-url= : Base URL do sistema (padrao APP_URL)}
                            {--append-log : Grava amostra no log de SLA}';

    protected $description = 'Coleta snapshot de health (live/ready) para evidencias operacionais';

    public function handle(): int
    {
        $baseUrl = rtrim((string) ($this->option('base-url') ?: config('app.url')), '/');
        if ($baseUrl === '') {
            $this->error('Base URL invalida. Configure APP_URL ou use --base-url.');
            return self::FAILURE;
        }

        $liveUrl = $baseUrl . '/api/health/live';
        $readyUrl = $baseUrl . '/api/health/ready';

        $live = $this->fetch($liveUrl);
        $ready = $this->fetch($readyUrl);

        $snapshot = [
            'timestamp' => now()->toIso8601String(),
            'base_url' => $baseUrl,
            'live' => $live,
            'ready' => $ready,
        ];

        $this->line(json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        if ($this->option('append-log')) {
            $path = (string) config('observability.sample_log_path');
            @mkdir(dirname($path), 0775, true);
            file_put_contents($path, json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
            $this->info('Amostra gravada em: ' . $path);
        }

        if (!(($live['ok'] ?? false) && ($ready['ok'] ?? false))) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function fetch(string $url): array
    {
        $start = microtime(true);
        try {
            $response = Http::timeout(5)->acceptJson()->get($url);
            $latency = (int) round((microtime(true) - $start) * 1000);
            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'latency_ms' => $latency,
                'body' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => 0,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'error' => $e->getMessage(),
            ];
        }
    }
}
