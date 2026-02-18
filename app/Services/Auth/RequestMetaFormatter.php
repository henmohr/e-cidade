<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

final class RequestMetaFormatter
{
    public const USER_AGENT_MAX_LENGTH = 300;

    public static function userAgent(Request $request): string
    {
        return substr((string) $request->userAgent(), 0, self::USER_AGENT_MAX_LENGTH);
    }
}
