<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthEventService
{
    private const USER_EVENTS_PREFIX = 'auth:events:user:';
    private const PENDING_FAILURES_PREFIX = 'auth:events:pending_failures:';
    private const MAX_EVENTS = 30;
    private const MAX_PENDING_FAILURES = 30;

    public function registerFailure(Request $request, string $identifier): void
    {
        $normalized = $this->normalizeIdentifier($identifier);
        if ($normalized === '') {
            return;
        }

        $key = $this->pendingFailuresKey($normalized);
        $entries = Cache::get($key, []);
        if (!is_array($entries)) {
            $entries = [];
        }

        $entries[] = [
            'type' => 'login_failed',
            'timestamp' => now()->toIso8601String(),
            'ip' => (string) $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 300),
        ];

        if (count($entries) > self::MAX_PENDING_FAILURES) {
            $entries = array_slice($entries, -self::MAX_PENDING_FAILURES);
        }

        Cache::put($key, $entries, now()->addDays($this->eventsRetentionDays()));
    }

    public function registerSuccess(Request $request, User $user): void
    {
        $this->appendUserEvent($user, [
            'type' => 'login_success',
            'timestamp' => now()->toIso8601String(),
            'ip' => (string) $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 300),
        ]);
    }

    public function absorbPendingFailuresForUser(User $user): int
    {
        $all = [];
        foreach ($this->identifiersForUser($user) as $identifier) {
            $key = $this->pendingFailuresKey($identifier);
            $entries = Cache::get($key, []);
            if (is_array($entries) && !empty($entries)) {
                $all = array_merge($all, $entries);
            }
            Cache::forget($key);
        }

        if (empty($all)) {
            return 0;
        }

        usort($all, function (array $a, array $b) {
            return strcmp((string) ($a['timestamp'] ?? ''), (string) ($b['timestamp'] ?? ''));
        });

        foreach ($all as $event) {
            $this->appendUserEvent($user, $event);
        }

        return count($all);
    }

    public function listRecentEventsForUser(User $user): array
    {
        $events = Cache::get($this->userEventsKey((int) $user->getAuthIdentifier()), []);
        if (!is_array($events)) {
            return [];
        }

        usort($events, function (array $a, array $b) {
            return strcmp((string) ($b['timestamp'] ?? ''), (string) ($a['timestamp'] ?? ''));
        });

        return array_values($events);
    }

    private function appendUserEvent(User $user, array $event): void
    {
        $key = $this->userEventsKey((int) $user->getAuthIdentifier());
        $events = Cache::get($key, []);
        if (!is_array($events)) {
            $events = [];
        }

        $events[] = $event;
        if (count($events) > self::MAX_EVENTS) {
            $events = array_slice($events, -self::MAX_EVENTS);
        }

        Cache::put($key, $events, now()->addDays($this->eventsRetentionDays()));
    }

    private function identifiersForUser(User $user): array
    {
        $values = [
            $this->normalizeIdentifier((string) ($user->login ?? '')),
            $this->normalizeIdentifier((string) ($user->cpf ?? '')),
        ];

        return array_values(array_filter(array_unique($values)));
    }

    private function normalizeIdentifier(string $value): string
    {
        return strtolower(trim($value));
    }

    private function userEventsKey(int $userId): string
    {
        return self::USER_EVENTS_PREFIX . $userId;
    }

    private function pendingFailuresKey(string $identifier): string
    {
        return self::PENDING_FAILURES_PREFIX . sha1($identifier);
    }

    private function eventsRetentionDays(): int
    {
        return max(1, (int) config('auth.auth_events.retention_days', 7));
    }
}
