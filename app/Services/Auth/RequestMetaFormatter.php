<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

final class RequestMetaFormatter
{
    public const USER_AGENT_MAX_LENGTH = 300;
    public const SESSION_PATH_MAX_LENGTH = 200;

    public static function userAgent(Request $request): string
    {
        return substr((string) $request->userAgent(), 0, self::USER_AGENT_MAX_LENGTH);
    }

    public static function normalizedPath(Request $request): string
    {
        return '/' . ltrim($request->path(), '/');
    }

    public static function sessionPath(Request $request): string
    {
        return substr((string) $request->path(), 0, self::SESSION_PATH_MAX_LENGTH);
    }
}
