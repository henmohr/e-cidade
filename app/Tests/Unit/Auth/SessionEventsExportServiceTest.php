<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventTypes;
use App\Services\Auth\AuthEventMetaKeys;
use App\Services\Auth\SessionEventsExportService;
use PHPUnit\Framework\TestCase;

class SessionEventsExportServiceTest extends TestCase
{
    public function testBuildCsvAndSha256AreDeterministic(): void
    {
        $service = new SessionEventsExportService();
        $events = [
            [
                AuthEventMetaKeys::TYPE => AuthEventTypes::SESSIONS_EXPORT_CSV,
                AuthEventMetaKeys::REQUEST_ID => 'req-1',
                AuthEventMetaKeys::TIMESTAMP => '2026-02-18T10:00:00Z',
                AuthEventMetaKeys::IP => '127.0.0.1',
                AuthEventMetaKeys::PROVIDER => '',
                AuthEventMetaKeys::REVOKED_COUNT => 2,
                AuthEventMetaKeys::FILE => 'auth.csv',
            ],
        ];

        $csv = $service->buildCsv($events);
        $this->assertStringContainsString(
            implode(',', SessionEventsExportService::CSV_HEADER),
            $csv
        );
        $this->assertStringContainsString(AuthEventTypes::SESSIONS_EXPORT_CSV . ',req-1', $csv);
        $this->assertStringContainsString(
            SessionEventsExportService::DETAILS_REVOKED_COUNT_KEY . '2'
                . SessionEventsExportService::DETAILS_PARTS_SEPARATOR
                . SessionEventsExportService::DETAILS_FILE_KEY . 'auth.csv',
            $csv
        );

        $hash = $service->computeSha256($csv);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }
}
