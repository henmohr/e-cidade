<?php

namespace App\Services\Auth;

class SessionEventsExportService
{
    public const CSV_DETAILS_COLUMN = 'details';
    /** @var array<int, string> */
    public const CSV_HEADER = [
        AuthEventMetaKeys::TYPE,
        AuthEventMetaKeys::REQUEST_ID,
        AuthEventMetaKeys::TIMESTAMP,
        AuthEventMetaKeys::IP,
        AuthEventMetaKeys::PROVIDER,
        self::CSV_DETAILS_COLUMN,
    ];
    public const DETAILS_PARTS_SEPARATOR = ';';
    public const DETAILS_REVOKED_COUNT_KEY = 'revoked_count=';
    public const DETAILS_TARGET_SESSION_ID_KEY = 'target_session_id=';
    public const DETAILS_TIER_KEY = 'tier=';
    public const DETAILS_FILE_KEY = 'file=';
    public const DETAILS_BLOCKED_SECONDS_KEY = 'blocked_seconds=';

    /**
     * @param array<int, array<string, mixed>> $events
     */
    public function buildCsv(array $events): string
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, self::CSV_HEADER);

        foreach ($events as $event) {
            fputcsv($out, [
                (string) ($event[AuthEventMetaKeys::TYPE] ?? ''),
                (string) ($event[AuthEventMetaKeys::REQUEST_ID] ?? ''),
                (string) ($event[AuthEventMetaKeys::TIMESTAMP] ?? ''),
                (string) ($event[AuthEventMetaKeys::IP] ?? ''),
                (string) ($event[AuthEventMetaKeys::PROVIDER] ?? ''),
                $this->detailsCsvColumn($event),
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return is_string($csv) ? $csv : '';
    }

    public function computeSha256(string $csv): string
    {
        return hash('sha256', $csv);
    }

    /**
     * @param array<string, mixed> $event
     */
    private function detailsCsvColumn(array $event): string
    {
        $parts = [];

        if (isset($event[AuthEventMetaKeys::REVOKED_COUNT])) {
            $parts[] = self::DETAILS_REVOKED_COUNT_KEY . (int) $event[AuthEventMetaKeys::REVOKED_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::TARGET_SESSION_ID])) {
            $parts[] = self::DETAILS_TARGET_SESSION_ID_KEY . (string) $event[AuthEventMetaKeys::TARGET_SESSION_ID];
        }

        if (!empty($event[AuthEventMetaKeys::TIER])) {
            $parts[] = self::DETAILS_TIER_KEY . (string) $event[AuthEventMetaKeys::TIER];
        }

        if (!empty($event[AuthEventMetaKeys::FILE])) {
            $parts[] = self::DETAILS_FILE_KEY . (string) $event[AuthEventMetaKeys::FILE];
        }

        if (isset($event[AuthEventMetaKeys::BLOCKED_SECONDS])) {
            $parts[] = self::DETAILS_BLOCKED_SECONDS_KEY . (int) $event[AuthEventMetaKeys::BLOCKED_SECONDS];
        }

        return implode(self::DETAILS_PARTS_SEPARATOR, $parts);
    }
}
