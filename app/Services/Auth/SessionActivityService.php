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
            SessionActivityKeys::SESSION_ID => $sessionId,
            SessionActivityKeys::STARTED_AT => $existing[SessionActivityKeys::STARTED_AT] ?? now()->toIso8601String(),
            SessionActivityKeys::LAST_SEEN_AT => now()->toIso8601String(),
            SessionActivityKeys::IP => (string) $request->ip(),
            SessionActivityKeys::USER_AGENT => RequestMetaFormatter::userAgent($request),
            SessionActivityKeys::PATH => RequestMetaFormatter::sessionPath($request),
        ];

        Cache::put($cacheKey, $sessions, now()->addMinutes($this->ttlMinutes()));
    }

    public function listForUser(User $user): array
    {
        $cacheKey = $this->userSessionsKey((int) $user->getAuthIdentifier());
        $sessions = Cache::get($cacheKey, []);
        if (!is_array($sessions)) {
            return [];
        }

        usort($sessions, function (array $a, array $b) {
            return strcmp(
                (string) ($b[SessionActivityKeys::LAST_SEEN_AT] ?? ''),
                (string) ($a[SessionActivityKeys::LAST_SEEN_AT] ?? '')
            );
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

        $ttlMinutes = $this->ttlMinutes();
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

        $ttlMinutes = $this->ttlMinutes();
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

    private function ttlMinutes(): int
    {
        return max(10, (int) config('session.lifetime', 120));
    }
}
