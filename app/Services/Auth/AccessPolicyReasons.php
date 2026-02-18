<?php

namespace App\Services\Auth;

final class AccessPolicyReasons
{
    public const DISABLED = 'disabled';
    public const ADMIN_BYPASS = 'admin_bypass';
    public const EXPIRED = 'expired';
    public const WEEKDAY = 'weekday';
    public const HOUR = 'hour';
    public const ALLOWED = 'allowed';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::DISABLED,
            self::ADMIN_BYPASS,
            self::EXPIRED,
            self::WEEKDAY,
            self::HOUR,
            self::ALLOWED,
        ];
    }
}
