<?php

namespace App\Services\Auth;

class SessionExportEvidencePresenter
{
    /**
     * @return array{verified: bool, message: string}
     */
    public function notFound(): array
    {
        return [
            'verified' => false,
            'message' => AuthMessages::EXPORT_HASH_NOT_FOUND,
        ];
    }

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>
     */
    public function verified(string $hash, array $event): array
    {
        return [
            'verified' => true,
            'hash' => strtolower(trim($hash)),
            'event_type' => (string) ($event[AuthEventMetaKeys::TYPE] ?? ''),
            'timestamp' => (string) ($event[AuthEventMetaKeys::TIMESTAMP] ?? ''),
            'request_id' => (string) ($event[AuthEventMetaKeys::REQUEST_ID] ?? ''),
            'row_count' => (int) ($event[AuthEventMetaKeys::ROW_COUNT] ?? 0),
        ];
    }
}
