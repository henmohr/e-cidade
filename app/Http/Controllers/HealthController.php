<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function live(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'ecidade',
            'check' => 'live',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function ready(): JsonResponse
    {
        $dbOk = false;
        $dbLatencyMs = null;
        $error = null;

        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $dbOk = true;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }
        $dbLatencyMs = (int) round((microtime(true) - $start) * 1000);

        $ready = $dbOk;
        $payload = [
            'status' => $ready ? 'ok' : 'degraded',
            'service' => 'ecidade',
            'check' => 'ready',
            'timestamp' => now()->toIso8601String(),
            'dependencies' => [
                'database' => [
                    'ok' => $dbOk,
                    'latency_ms' => $dbLatencyMs,
                ],
            ],
        ];

        if (!$dbOk && !empty($error)) {
            $payload['dependencies']['database']['error'] = $error;
        }

        return response()->json($payload, $ready ? 200 : 503);
    }
}
