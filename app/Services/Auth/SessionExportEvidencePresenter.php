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
            'event_type' => (string) ($event['type'] ?? ''),
            'timestamp' => (string) ($event['timestamp'] ?? ''),
            'request_id' => (string) ($event['request_id'] ?? ''),
            'row_count' => (int) ($event['row_count'] ?? 0),
        ];
    }
}
