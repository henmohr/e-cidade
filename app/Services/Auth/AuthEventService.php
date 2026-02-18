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

        $entries[] = array_merge(
            [AuthEventMetaKeys::TYPE => AuthEventTypes::LOGIN_FAILED],
            $this->baseEventMeta($request)
        );

        if (count($entries) > self::MAX_PENDING_FAILURES) {
            $entries = array_slice($entries, -self::MAX_PENDING_FAILURES);
        }

        Cache::put($key, $entries, now()->addDays($this->eventsRetentionDays()));
    }

    public function registerSuccess(Request $request, User $user): void
    {
        $this->appendUserEvent($user, array_merge(
            [AuthEventMetaKeys::TYPE => AuthEventTypes::LOGIN_SUCCESS],
            $this->baseEventMeta($request)
        ));
    }

    public function registerExternalSuccess(Request $request, User $user, string $provider): void
    {
        $this->appendUserEvent($user, array_merge(
            [
                AuthEventMetaKeys::TYPE => AuthEventTypes::LOGIN_EXTERNAL_SUCCESS,
                AuthEventMetaKeys::PROVIDER => $this->normalizeIdentifier($provider),
            ],
            $this->baseEventMeta($request)
        ));
    }

    public function registerLogout(Request $request, User $user): void
    {
        $this->appendUserEvent($user, array_merge(
            [AuthEventMetaKeys::TYPE => AuthEventTypes::LOGOUT],
            $this->baseEventMeta($request)
        ));
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function registerCustomEvent(Request $request, User $user, string $type, array $meta = []): void
    {
        $type = strtolower(trim($type));
        if ($type === '') {
            return;
        }

        $event = array_merge(
            $meta,
            [AuthEventMetaKeys::TYPE => $type],
            $this->baseEventMeta($request)
        );

        $this->appendUserEvent($user, $event);
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listRecentEventsForUserFiltered(
        User $user,
        ?string $type = null,
        ?string $requestId = null,
        int $limit = SessionEventFilters::DEFAULT_SCREEN_LIMIT
    ): array {
        $events = $this->listRecentEventsForUser($user);
        $type = strtolower(trim((string) $type));
        $requestId = trim((string) $requestId);

        $events = array_values(array_filter($events, static function (array $event) use ($type, $requestId): bool {
            if ($type !== '' && strtolower((string) ($event[AuthEventMetaKeys::TYPE] ?? '')) !== $type) {
                return false;
            }

            if ($requestId !== '' && stripos((string) ($event[AuthEventMetaKeys::REQUEST_ID] ?? ''), $requestId) === false) {
                return false;
            }

            return true;
        }));

        $limit = max(SessionEventFilters::MIN_LIMIT, min(SessionEventFilters::MAX_LIMIT, $limit));
        return array_slice($events, 0, $limit);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findRecentExportEventByHash(User $user, string $sha256): ?array
    {
        $sha256 = ExportHash::normalize($sha256);
        if (!ExportHash::isValid($sha256)) {
            return null;
        }

        $events = $this->listRecentEventsForUserFiltered(
            $user,
            AuthEventTypes::SESSIONS_EXPORT_CSV,
            null,
            SessionEventFilters::DEFAULT_EXPORT_LIMIT
        );
        foreach ($events as $event) {
            $hash = strtolower((string) ($event[AuthEventMetaKeys::EXPORT_SHA256] ?? ''));
            if ($hash === $sha256) {
                return $event;
            }
        }

        return null;
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

    /**
     * @return array{request_id: string, timestamp: string, ip: string, user_agent: string}
     */
    private function baseEventMeta(Request $request): array
    {
        return [
            AuthEventMetaKeys::REQUEST_ID => RequestIdResolver::resolve($request),
            AuthEventMetaKeys::TIMESTAMP => now()->toIso8601String(),
            AuthEventMetaKeys::IP => (string) $request->ip(),
            AuthEventMetaKeys::USER_AGENT => RequestMetaFormatter::userAgent($request),
        ];
    }
}
