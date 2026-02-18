<?php

namespace App\Services\Auth;

use App\Support\Session\LegacySession;
use Illuminate\Http\Request;

class WebAuditTrailService
{
    public function isEnabled(): bool
    {
        return (bool) config('web_audit.enabled', true);
    }

    public function shouldSkipPath(string $path): bool
    {
        $path = trim($path, '/');
        $excluded = (array) config('web_audit.exclude_paths', []);

        foreach ($excluded as $item) {
            $item = trim((string) $item, '/');
            if ($item !== '' && $path === $item) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildContext(Request $request, int $statusCode, int $durationMs): array
    {
        $user = null;
        try {
            if (app()->bound('auth')) {
                $user = app('auth')->user();
            }
        } catch (\Throwable $e) {
            $user = null;
        }
        $query = $request->query();
        $sessionId = '';
        $instit = null;
        $module = null;
        if ($request->hasSession()) {
            $sessionId = (string) $request->session()->getId();
            $instit = $request->session()->get(LegacySession::DB_INSTIT);
            $module = $request->session()->get(LegacySession::DB_NOME_MODULO);
        }

        $context = [
            'request_id' => RequestIdResolver::resolve($request),
            'user_id' => $user?->getAuthIdentifier(),
            'login' => $user->login ?? null,
            'instit' => $instit,
            'session_id' => $sessionId,
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'route_name' => optional($request->route())->getName(),
            'status' => $statusCode,
            'duration_ms' => $durationMs,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 300),
            'module' => $module,
        ];

        if ((bool) config('web_audit.include_query', true) && is_array($query) && !empty($query)) {
            $context['query_keys'] = array_keys($query);
        }

        if ((bool) config('web_audit.include_input_keys', true)) {
            $context['input_keys'] = $this->safeInputKeys($request);
        }

        return $context;
    }

    /**
     * @return array<int, string>
     */
    private function safeInputKeys(Request $request): array
    {
        $keys = array_keys($request->all());
        $sensitive = array_map('strtolower', (array) config('web_audit.sensitive_keys', []));

        $filtered = array_filter($keys, static function ($key) use ($sensitive) {
            return !in_array(strtolower((string) $key), $sensitive, true);
        });

        return array_values($filtered);
    }
}
