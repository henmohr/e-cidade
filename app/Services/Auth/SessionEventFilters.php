<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

class SessionEventFilters
{
    private string $eventType;
    private string $eventRequestId;
    private int $eventLimit;

    public function __construct(string $eventType = '', string $eventRequestId = '', int $eventLimit = 50)
    {
        $this->eventType = trim($eventType);
        $this->eventRequestId = trim($eventRequestId);
        $this->eventLimit = $this->normalizeLimit($eventLimit);
    }

    public static function fromRequest(Request $request, int $defaultLimit = 50): self
    {
        return new self(
            (string) $request->query('event_type', ''),
            (string) $request->query('event_request_id', ''),
            (int) $request->query('event_limit', $defaultLimit)
        );
    }

    public function eventType(): string
    {
        return $this->eventType;
    }

    public function eventRequestId(): string
    {
        return $this->eventRequestId;
    }

    public function eventLimit(): int
    {
        return $this->eventLimit;
    }

    /**
     * @return array{event_type: string, event_request_id: string, event_limit: int}
     */
    public function toArray(): array
    {
        return [
            'event_type' => $this->eventType,
            'event_request_id' => $this->eventRequestId,
            'event_limit' => $this->eventLimit,
        ];
    }

    private function normalizeLimit(int $limit): int
    {
        return max(1, min(200, $limit));
    }
}
