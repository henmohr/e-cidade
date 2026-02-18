<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventTypes;
use App\Services\Auth\SessionEventsExportService;
use PHPUnit\Framework\TestCase;

class SessionEventsExportServiceTest extends TestCase
{
    public function testBuildCsvAndSha256AreDeterministic(): void
    {
        $service = new SessionEventsExportService();
        $events = [
            [
                'type' => AuthEventTypes::SESSIONS_EXPORT_CSV,
                'request_id' => 'req-1',
                'timestamp' => '2026-02-18T10:00:00Z',
                'ip' => '127.0.0.1',
                'provider' => '',
                'revoked_count' => 2,
                'file' => 'auth.csv',
            ],
        ];

        $csv = $service->buildCsv($events);
        $this->assertStringContainsString('type,request_id,timestamp,ip,provider,details', $csv);
        $this->assertStringContainsString(AuthEventTypes::SESSIONS_EXPORT_CSV . ',req-1', $csv);
        $this->assertStringContainsString('revoked_count=2;file=auth.csv', $csv);

        $hash = $service->computeSha256($csv);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }
}
