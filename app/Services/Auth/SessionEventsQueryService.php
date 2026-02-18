<?php

namespace App\Services\Auth;

use App\Models\User;

class SessionEventsQueryService
{
    private AuthEventService $eventService;
    private AuthEventPresenter $presenter;

    public function __construct(AuthEventService $eventService, AuthEventPresenter $presenter)
    {
        $this->eventService = $eventService;
        $this->presenter = $presenter;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function eventsForScreen(User $user, SessionEventFilters $filters): array
    {
        $events = $this->eventService->listRecentEventsForUserFiltered(
            $user,
            $filters->eventType(),
            $filters->eventRequestId(),
            $filters->eventLimit()
        );

        return array_map(function (array $event): array {
            $event['type_label'] = $this->presenter->typeLabel($event);
            $event['details'] = $this->presenter->details($event);
            return $event;
        }, $events);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rawFilteredEvents(User $user, SessionEventFilters $filters): array
    {
        return $this->eventService->listRecentEventsForUserFiltered(
            $user,
            $filters->eventType(),
            $filters->eventRequestId(),
            $filters->eventLimit()
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findExportHash(User $user, string $sha256): ?array
    {
        return $this->eventService->findRecentExportEventByHash($user, $sha256);
    }
}
