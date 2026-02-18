<?php

namespace App\Services\Auth;

final class AuthEventMetaKeys
{
    public const TYPE = 'type';
    public const REQUEST_ID = 'request_id';
    public const TIMESTAMP = 'timestamp';
    public const IP = 'ip';
    public const USER_AGENT = 'user_agent';
    public const PROVIDER = 'provider';
    public const REVOKED_COUNT = 'revoked_count';
    public const TARGET_SESSION_ID = 'target_session_id';
    public const TIER = 'tier';
    public const FILE = 'file';
    public const BLOCKED_SECONDS = 'blocked_seconds';
    public const ROW_COUNT = 'row_count';
    public const EXPORT_SHA256 = 'export_sha256';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::TYPE,
            self::REQUEST_ID,
            self::TIMESTAMP,
            self::IP,
            self::USER_AGENT,
            self::PROVIDER,
            self::REVOKED_COUNT,
            self::TARGET_SESSION_ID,
            self::TIER,
            self::FILE,
            self::BLOCKED_SECONDS,
            self::ROW_COUNT,
            self::EXPORT_SHA256,
        ];
    }
}
