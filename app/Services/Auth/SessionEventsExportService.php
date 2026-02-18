<?php

namespace App\Services\Auth;

class SessionEventsExportService
{
    /** @var array<int, string> */
    public const CSV_HEADER = ['type', 'request_id', 'timestamp', 'ip', 'provider', 'details'];
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
                (string) ($event['type'] ?? ''),
                (string) ($event['request_id'] ?? ''),
                (string) ($event['timestamp'] ?? ''),
                (string) ($event['ip'] ?? ''),
                (string) ($event['provider'] ?? ''),
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

        if (isset($event['revoked_count'])) {
            $parts[] = 'revoked_count=' . (int) $event['revoked_count'];
        }

        if (!empty($event['target_session_id'])) {
            $parts[] = 'target_session_id=' . (string) $event['target_session_id'];
        }

        if (!empty($event['tier'])) {
            $parts[] = 'tier=' . (string) $event['tier'];
        }

        if (!empty($event['file'])) {
            $parts[] = 'file=' . (string) $event['file'];
        }

        if (isset($event['blocked_seconds'])) {
            $parts[] = 'blocked_seconds=' . (int) $event['blocked_seconds'];
        }

        return implode(self::DETAILS_PARTS_SEPARATOR, $parts);
    }
}
