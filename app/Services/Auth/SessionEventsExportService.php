<?php

namespace App\Services\Auth;

class SessionEventsExportService
{
    /** @var array<int, string> */
    public const CSV_HEADER = [
        AuthEventMetaKeys::TYPE,
        AuthEventMetaKeys::REQUEST_ID,
        AuthEventMetaKeys::TIMESTAMP,
        AuthEventMetaKeys::IP,
        AuthEventMetaKeys::PROVIDER,
        'details',
    ];
    public const DETAILS_PARTS_SEPARATOR = ';';

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
            $parts[] = 'revoked_count=' . (int) $event[AuthEventMetaKeys::REVOKED_COUNT];
        }

        if (!empty($event[AuthEventMetaKeys::TARGET_SESSION_ID])) {
            $parts[] = 'target_session_id=' . (string) $event[AuthEventMetaKeys::TARGET_SESSION_ID];
        }

        if (!empty($event[AuthEventMetaKeys::TIER])) {
            $parts[] = 'tier=' . (string) $event[AuthEventMetaKeys::TIER];
        }

        if (!empty($event[AuthEventMetaKeys::FILE])) {
            $parts[] = 'file=' . (string) $event[AuthEventMetaKeys::FILE];
        }

        if (isset($event[AuthEventMetaKeys::BLOCKED_SECONDS])) {
            $parts[] = 'blocked_seconds=' . (int) $event[AuthEventMetaKeys::BLOCKED_SECONDS];
        }

        return implode(self::DETAILS_PARTS_SEPARATOR, $parts);
    }
}
