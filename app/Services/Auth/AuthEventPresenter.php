<?php

namespace App\Services\Auth;

class AuthEventPresenter
{
    private const DETAILS_SEPARATOR = ' | ';
    private const UNKNOWN_DETAILS = '-';

    private const PREFIX_PROVIDER = 'provider=';
    private const PREFIX_REVOKED = 'revoked=';
    private const PREFIX_TARGET = 'target=';
    private const PREFIX_TIER = 'tier=';
    private const PREFIX_FILE = 'file=';
    private const PREFIX_BLOCKED = 'blocked=';
    private const PREFIX_ROWS = 'rows=';
    private const PREFIX_SHA256 = 'sha256=';

    private const BLOCKED_SUFFIX = 's';
    private const HASH_SUFFIX = '...';
    private const TARGET_MAX_LENGTH = 40;
    private const FILE_MAX_LENGTH = 80;
    private const HASH_PREVIEW_LENGTH = 16;

    /**
     * @param array<string, mixed> $event
     */
    public function typeLabel(array $event): string
    {
        $type = strtolower(trim((string) ($event['type'] ?? '')));

        $labels = AuthEventLabels::byType();

        return $labels[$type] ?? ($type !== '' ? $type : self::UNKNOWN_DETAILS);
    }

    /**
     * @param array<string, mixed> $event
     */
    public function details(array $event): string
    {
        $parts = [];

        if (!empty($event[AuthEventMetaKeys::PROVIDER])) {
            $parts[] = self::PREFIX_PROVIDER . strtolower(trim((string) $event[AuthEventMetaKeys::PROVIDER]));
        }

        if (!empty($event[AuthEventMetaKeys::REVOKED_COUNT])) {
            $parts[] = self::PREFIX_REVOKED . (int) $event[AuthEventMetaKeys::REVOKED_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::TARGET_SESSION_ID])) {
            $parts[] = self::PREFIX_TARGET . substr(
                (string) $event[AuthEventMetaKeys::TARGET_SESSION_ID],
                0,
                self::TARGET_MAX_LENGTH
            );
        }

        if (!empty($event[AuthEventMetaKeys::TIER])) {
            $parts[] = self::PREFIX_TIER . strtolower(trim((string) $event[AuthEventMetaKeys::TIER]));
        }

        if (!empty($event[AuthEventMetaKeys::FILE])) {
            $parts[] = self::PREFIX_FILE . substr((string) $event[AuthEventMetaKeys::FILE], 0, self::FILE_MAX_LENGTH);
        }

        if (!empty($event[AuthEventMetaKeys::BLOCKED_SECONDS])) {
            $parts[] = self::PREFIX_BLOCKED . (int) $event[AuthEventMetaKeys::BLOCKED_SECONDS] . self::BLOCKED_SUFFIX;
        }

        if (isset($event[AuthEventMetaKeys::ROW_COUNT])) {
            $parts[] = self::PREFIX_ROWS . (int) $event[AuthEventMetaKeys::ROW_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::EXPORT_SHA256])) {
            $parts[] = self::PREFIX_SHA256
                . substr((string) $event[AuthEventMetaKeys::EXPORT_SHA256], 0, self::HASH_PREVIEW_LENGTH)
                . self::HASH_SUFFIX;
        }

        if (empty($parts)) {
            return self::UNKNOWN_DETAILS;
        }

        return implode(self::DETAILS_SEPARATOR, $parts);
    }
}
