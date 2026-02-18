<?php

namespace App\Services\Auth;

use App\Support\Session\LegacySession;
use Illuminate\Http\Request;

class WebAuditTrailService
{
    private const CONTEXT_REQUEST_ID = 'request_id';
    private const CONTEXT_USER_ID = 'user_id';
    private const CONTEXT_LOGIN = 'login';
    private const CONTEXT_INSTIT = 'instit';
    private const CONTEXT_SESSION_ID = 'session_id';
    private const CONTEXT_METHOD = 'method';
    private const CONTEXT_PATH = 'path';
    private const CONTEXT_ROUTE_NAME = 'route_name';
    private const CONTEXT_STATUS = 'status';
    private const CONTEXT_DURATION_MS = 'duration_ms';
    private const CONTEXT_IP = 'ip';
    private const CONTEXT_USER_AGENT = 'user_agent';
    private const CONTEXT_MODULE = 'module';
    private const CONTEXT_QUERY_KEYS = 'query_keys';
    private const CONTEXT_INPUT_KEYS = 'input_keys';

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
            self::CONTEXT_REQUEST_ID => RequestIdResolver::resolve($request),
            self::CONTEXT_USER_ID => $user?->getAuthIdentifier(),
            self::CONTEXT_LOGIN => $user->login ?? null,
            self::CONTEXT_INSTIT => $instit,
            self::CONTEXT_SESSION_ID => $sessionId,
            self::CONTEXT_METHOD => $request->method(),
            self::CONTEXT_PATH => RequestMetaFormatter::normalizedPath($request),
            self::CONTEXT_ROUTE_NAME => optional($request->route())->getName(),
            self::CONTEXT_STATUS => $statusCode,
            self::CONTEXT_DURATION_MS => $durationMs,
            self::CONTEXT_IP => $request->ip(),
            self::CONTEXT_USER_AGENT => RequestMetaFormatter::userAgent($request),
            self::CONTEXT_MODULE => $module,
        ];

        if ((bool) config('web_audit.include_query', true) && is_array($query) && !empty($query)) {
            $context[self::CONTEXT_QUERY_KEYS] = array_keys($query);
        }

        if ((bool) config('web_audit.include_input_keys', true)) {
            $context[self::CONTEXT_INPUT_KEYS] = $this->safeInputKeys($request);
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
