<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventTypes;
use App\Models\User;
use App\Services\Auth\AuthEventMetaKeys;
use App\Services\Auth\AuthEventPresenter;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\SessionEventFilters;
use App\Services\Auth\SessionEventsQueryService;
use Mockery;
use PHPUnit\Framework\TestCase;

class SessionEventsQueryServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testEnrichesEventsForScreenWithLabelsAndDetails(): void
    {
        $eventService = Mockery::mock(AuthEventService::class);
        $presenter = Mockery::mock(AuthEventPresenter::class);
        $service = new SessionEventsQueryService($eventService, $presenter);

        $user = Mockery::mock(User::class);
        $filters = new SessionEventFilters(AuthEventTypes::LOGIN_SUCCESS, 'req-1', 10);

        $rawEvents = [[
            AuthEventMetaKeys::TYPE => AuthEventTypes::LOGIN_SUCCESS,
            AuthEventMetaKeys::REQUEST_ID => 'req-1',
        ]];

        $eventService->shouldReceive('listRecentEventsForUserFiltered')
            ->once()
            ->with($user, AuthEventTypes::LOGIN_SUCCESS, 'req-1', 10)
            ->andReturn($rawEvents);

        $presenter->shouldReceive('typeLabel')->once()->andReturn('Login com sucesso');
        $presenter->shouldReceive('details')->once()->andReturn('provider=govbr');

        $events = $service->eventsForScreen($user, $filters);
        $this->assertSame('Login com sucesso', $events[0]['type_label']);
        $this->assertSame('provider=govbr', $events[0]['details']);
    }

    public function testDelegatesFindExportHashToEventService(): void
    {
        $eventService = Mockery::mock(AuthEventService::class);
        $presenter = Mockery::mock(AuthEventPresenter::class);
        $service = new SessionEventsQueryService($eventService, $presenter);

        $user = Mockery::mock(User::class);
        $eventService->shouldReceive('findRecentExportEventByHash')
            ->once()
            ->with($user, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
            ->andReturn([AuthEventMetaKeys::TYPE => AuthEventTypes::SESSIONS_EXPORT_CSV]);

        $event = $service->findExportHash($user, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $this->assertSame(AuthEventTypes::SESSIONS_EXPORT_CSV, $event[AuthEventMetaKeys::TYPE]);
    }
}
