<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SessionActivityService
{
    private const USER_SESSIONS_PREFIX = 'auth:sessions:user:';
    private const REVOKED_PREFIX = 'auth:sessions:revoked:';

    public function touch(User $user, Request $request): void
    {
        $sessionId = (string) $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        $cacheKey = $this->userSessionsKey((int) $user->getAuthIdentifier());
        $sessions = Cache::get($cacheKey, []);
        if (!is_array($sessions)) {
            $sessions = [];
        }

        $existing = $sessions[$sessionId] ?? [];
        $sessions[$sessionId] = [
            'session_id' => $sessionId,
            'started_at' => $existing['started_at'] ?? now()->toIso8601String(),
            'last_seen_at' => now()->toIso8601String(),
            'ip' => (string) $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 300),
            'path' => substr((string) $request->path(), 0, 200),
        ];

        $ttlMinutes = max(10, (int) config('session.lifetime', 120));
        Cache::put($cacheKey, $sessions, now()->addMinutes($ttlMinutes));
    }

    public function listForUser(User $user): array
    {
        $cacheKey = $this->userSessionsKey((int) $user->getAuthIdentifier());
        $sessions = Cache::get($cacheKey, []);
        if (!is_array($sessions)) {
            return [];
        }

        usort($sessions, function (array $a, array $b) {
            return strcmp((string) ($b['last_seen_at'] ?? ''), (string) ($a['last_seen_at'] ?? ''));
        });

        return array_values($sessions);
    }

    public function revokeSession(User $user, string $sessionId): bool
    {
        $sessionId = trim($sessionId);
        if ($sessionId === '') {
            return false;
        }

        $cacheKey = $this->userSessionsKey((int) $user->getAuthIdentifier());
        $sessions = Cache::get($cacheKey, []);
        if (!is_array($sessions) || !isset($sessions[$sessionId])) {
            return false;
        }

        unset($sessions[$sessionId]);

        $ttlMinutes = max(10, (int) config('session.lifetime', 120));
        Cache::put($cacheKey, $sessions, now()->addMinutes($ttlMinutes));
        Cache::put($this->revokedSessionKey($sessionId), true, now()->addMinutes($ttlMinutes));

        return true;
    }

    public function revokeOtherSessions(User $user, string $keepSessionId): int
    {
        $keepSessionId = trim($keepSessionId);
        if ($keepSessionId === '') {
            return 0;
        }

        $cacheKey = $this->userSessionsKey((int) $user->getAuthIdentifier());
        $sessions = Cache::get($cacheKey, []);
        if (!is_array($sessions) || empty($sessions)) {
            return 0;
        }

        $ttlMinutes = max(10, (int) config('session.lifetime', 120));
        $revoked = 0;
        foreach (array_keys($sessions) as $sessionId) {
            if ((string) $sessionId === $keepSessionId) {
                continue;
            }

            unset($sessions[$sessionId]);
            Cache::put($this->revokedSessionKey((string) $sessionId), true, now()->addMinutes($ttlMinutes));
            $revoked++;
        }

        Cache::put($cacheKey, $sessions, now()->addMinutes($ttlMinutes));

        return $revoked;
    }

    public function isRevoked(string $sessionId): bool
    {
        if ($sessionId === '') {
            return false;
        }

        return (bool) Cache::get($this->revokedSessionKey($sessionId), false);
    }

    private function userSessionsKey(int $userId): string
    {
        return self::USER_SESSIONS_PREFIX . $userId;
    }

    private function revokedSessionKey(string $sessionId): string
    {
        return self::REVOKED_PREFIX . $sessionId;
    }
}
