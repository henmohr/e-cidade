<?php

namespace App\Services\Auth;

class SessionExportEvidencePresenter
{
    private const KEY_VERIFIED = 'verified';
    private const KEY_MESSAGE = 'message';
    private const KEY_HASH = 'hash';
    private const KEY_EVENT_TYPE = 'event_type';
    private const KEY_TIMESTAMP = 'timestamp';
    private const KEY_REQUEST_ID = 'request_id';
    private const KEY_ROW_COUNT = 'row_count';

    /**
     * @return array{verified: bool, message: string}
     */
    public function notFound(): array
    {
        return [
            self::KEY_VERIFIED => false,
            self::KEY_MESSAGE => AuthMessages::EXPORT_HASH_NOT_FOUND,
        ];
    }

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>
     */
    public function verified(string $hash, array $event): array
    {
        return [
            self::KEY_VERIFIED => true,
            self::KEY_HASH => ExportHash::normalize($hash),
            self::KEY_EVENT_TYPE => (string) ($event[AuthEventMetaKeys::TYPE] ?? ''),
            self::KEY_TIMESTAMP => (string) ($event[AuthEventMetaKeys::TIMESTAMP] ?? ''),
            self::KEY_REQUEST_ID => (string) ($event[AuthEventMetaKeys::REQUEST_ID] ?? ''),
            self::KEY_ROW_COUNT => (int) ($event[AuthEventMetaKeys::ROW_COUNT] ?? 0),
        ];
    }
}
