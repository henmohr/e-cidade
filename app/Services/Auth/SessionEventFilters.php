<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

class SessionEventFilters
{
    public const QUERY_EVENT_TYPE = 'event_type';
    public const QUERY_EVENT_REQUEST_ID = 'event_request_id';
    public const QUERY_EVENT_LIMIT = 'event_limit';

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
            (string) $request->query(self::QUERY_EVENT_TYPE, ''),
            (string) $request->query(self::QUERY_EVENT_REQUEST_ID, ''),
            (int) $request->query(self::QUERY_EVENT_LIMIT, $defaultLimit)
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
            self::QUERY_EVENT_TYPE => $this->eventType,
            self::QUERY_EVENT_REQUEST_ID => $this->eventRequestId,
            self::QUERY_EVENT_LIMIT => $this->eventLimit,
        ];
    }

    private function normalizeLimit(int $limit): int
    {
        return max(self::MIN_LIMIT, min(self::MAX_LIMIT, $limit));
    }
}
