<?php

namespace App\Services\Auth;

final class ExportHash
{
    public const LENGTH = 64;
    public const REGEX = '/^[a-f0-9]{64}$/';
    public const VALIDATION_RULE = 'regex:/^[a-fA-F0-9]{64}$/';

    public static function normalize(string $value): string
    {
        return strtolower(trim($value));
    }

    public static function isValid(string $value): bool
    {
        return preg_match(self::REGEX, self::normalize($value)) === 1;
    }
}
