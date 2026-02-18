<?php

namespace App\Services\Auth;

class AuthEventPresenter
{
    /**
     * @param array<string, mixed> $event
     */
    public function typeLabel(array $event): string
    {
        $type = strtolower(trim((string) ($event['type'] ?? '')));

        $labels = AuthEventLabels::byType();

        return $labels[$type] ?? ($type !== '' ? $type : '-');
    }

    /**
     * @param array<string, mixed> $event
     */
    public function details(array $event): string
    {
        $parts = [];

        if (!empty($event[AuthEventMetaKeys::PROVIDER])) {
            $parts[] = 'provider=' . strtolower(trim((string) $event[AuthEventMetaKeys::PROVIDER]));
        }

        if (!empty($event[AuthEventMetaKeys::REVOKED_COUNT])) {
            $parts[] = 'revoked=' . (int) $event[AuthEventMetaKeys::REVOKED_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::TARGET_SESSION_ID])) {
            $parts[] = 'target=' . substr((string) $event[AuthEventMetaKeys::TARGET_SESSION_ID], 0, 40);
        }

        if (!empty($event[AuthEventMetaKeys::TIER])) {
            $parts[] = 'tier=' . strtolower(trim((string) $event[AuthEventMetaKeys::TIER]));
        }

        if (!empty($event[AuthEventMetaKeys::FILE])) {
            $parts[] = 'file=' . substr((string) $event[AuthEventMetaKeys::FILE], 0, 80);
        }

        if (!empty($event[AuthEventMetaKeys::BLOCKED_SECONDS])) {
            $parts[] = 'blocked=' . (int) $event[AuthEventMetaKeys::BLOCKED_SECONDS] . 's';
        }

        if (isset($event[AuthEventMetaKeys::ROW_COUNT])) {
            $parts[] = 'rows=' . (int) $event[AuthEventMetaKeys::ROW_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::EXPORT_SHA256])) {
            $parts[] = 'sha256=' . substr((string) $event[AuthEventMetaKeys::EXPORT_SHA256], 0, 16) . '...';
        }

        if (empty($parts)) {
            return '-';
        }

        return implode(' | ', $parts);
    }
}
