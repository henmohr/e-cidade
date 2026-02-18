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
            SessionEventFilters::QUERY_EVENT_TYPE => ' login_success ',
            SessionEventFilters::QUERY_EVENT_REQUEST_ID => ' req-123 ',
            SessionEventFilters::QUERY_EVENT_LIMIT => 999,
        ]);

        $filters = SessionEventFilters::fromRequest($request, 50);

        $this->assertSame('login_success', $filters->eventType());
        $this->assertSame('req-123', $filters->eventRequestId());
        $this->assertSame(200, $filters->eventLimit());
    }

    public function testAppliesDefaultLimitAndLowerBound(): void
    {
        $request = Request::create('/web/sessions', 'GET', [
            SessionEventFilters::QUERY_EVENT_LIMIT => 0,
        ]);

        $filters = SessionEventFilters::fromRequest($request, 50);
        $this->assertSame(1, $filters->eventLimit());
        $this->assertSame([
            SessionEventFilters::QUERY_EVENT_TYPE => '',
            SessionEventFilters::QUERY_EVENT_REQUEST_ID => '',
            SessionEventFilters::QUERY_EVENT_LIMIT => 1,
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
