<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;

final class RequestIdResolver
{
    public static function resolve(Request $request): string
    {
        $attributeId = trim((string) $request->attributes->get('request_id', ''));
        if ($attributeId !== '') {
            return $attributeId;
        }

        return trim((string) $request->headers->get('X-Request-Id', ''));
    }
}
