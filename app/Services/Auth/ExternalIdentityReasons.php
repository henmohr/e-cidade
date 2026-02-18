<?php

namespace App\Services\Auth;

final class ExternalIdentityReasons
{
    public const DISABLED = 'disabled';
    public const PROVIDER_NOT_ALLOWED = 'provider_not_allowed';
    public const INVALID_SIGNATURE = 'invalid_signature';
    public const INVALID_PAYLOAD = 'invalid_payload';
    public const EXPIRED_CLAIMS = 'expired_claims';
    public const NONCE_REUSED_OR_MISSING = 'nonce_reused_or_missing';
    public const USER_NOT_FOUND = 'user_not_found';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::DISABLED,
            self::PROVIDER_NOT_ALLOWED,
            self::INVALID_SIGNATURE,
            self::INVALID_PAYLOAD,
            self::EXPIRED_CLAIMS,
            self::NONCE_REUSED_OR_MISSING,
            self::USER_NOT_FOUND,
        ];
    }
}
