<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

class SessionEventFilters
{
    public const MIN_LIMIT = 1;
    public const MAX_LIMIT = 200;
    public const DEFAULT_SCREEN_LIMIT = 50;
    public const DEFAULT_EXPORT_LIMIT = 200;

    private string $eventType;
    private string $eventRequestId;
    private int $eventLimit;

    public function __construct(
        string $eventType = '',
        string $eventRequestId = '',
        int $eventLimit = self::DEFAULT_SCREEN_LIMIT
    )
    {
        $this->eventType = trim($eventType);
        $this->eventRequestId = trim($eventRequestId);
        $this->eventLimit = $this->normalizeLimit($eventLimit);
    }

    public static function fromRequest(Request $request, int $defaultLimit = self::DEFAULT_SCREEN_LIMIT): self
    {
        return new self(
            (string) $request->query('event_type', ''),
            (string) $request->query('event_request_id', ''),
            (int) $request->query('event_limit', $defaultLimit)
        );
    }

    public static function fromScreenRequest(Request $request): self
    {
        return self::fromRequest($request, self::DEFAULT_SCREEN_LIMIT);
    }

    public static function fromExportRequest(Request $request): self
    {
        return self::fromRequest($request, self::DEFAULT_EXPORT_LIMIT);
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
        return max(self::MIN_LIMIT, min(self::MAX_LIMIT, $limit));
    }
}
