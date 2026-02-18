<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\SessionEventFilters;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class SessionEventFiltersTest extends TestCase
{
    public function testBuildsFiltersFromRequestAndNormalizesLimit(): void
    {
        $request = Request::create('/web/sessions', 'GET', [
            'event_type' => ' login_success ',
            'event_request_id' => ' req-123 ',
            'event_limit' => 999,
        ]);

        $filters = SessionEventFilters::fromRequest($request, 50);

        $this->assertSame('login_success', $filters->eventType());
        $this->assertSame('req-123', $filters->eventRequestId());
        $this->assertSame(200, $filters->eventLimit());
    }

    public function testAppliesDefaultLimitAndLowerBound(): void
    {
        $request = Request::create('/web/sessions', 'GET', [
            'event_limit' => 0,
        ]);

        $filters = SessionEventFilters::fromRequest($request, 50);
        $this->assertSame(1, $filters->eventLimit());
        $this->assertSame([
            'event_type' => '',
            'event_request_id' => '',
            'event_limit' => 1,
        ], $filters->toArray());
    }

    public function testUsesSemanticDefaultFactories(): void
    {
        $request = Request::create('/web/sessions', 'GET');

        $screenFilters = SessionEventFilters::fromScreenRequest($request);
        $exportFilters = SessionEventFilters::fromExportRequest($request);

        $this->assertSame(SessionEventFilters::DEFAULT_SCREEN_LIMIT, $screenFilters->eventLimit());
        $this->assertSame(SessionEventFilters::DEFAULT_EXPORT_LIMIT, $exportFilters->eventLimit());
    }
}
