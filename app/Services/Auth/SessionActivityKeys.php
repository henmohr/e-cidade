<?php

namespace App\Services\Auth;

final class SessionActivityKeys
{
    public const SESSION_ID = 'session_id';
    public const STARTED_AT = 'started_at';
    public const LAST_SEEN_AT = 'last_seen_at';
    public const IP = 'ip';
    public const USER_AGENT = 'user_agent';
    public const PATH = 'path';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::SESSION_ID,
            self::STARTED_AT,
            self::LAST_SEEN_AT,
            self::IP,
            self::USER_AGENT,
            self::PATH,
        ];
    }
}
