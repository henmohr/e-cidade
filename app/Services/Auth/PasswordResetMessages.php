<?php

namespace App\Services\Auth;

final class PasswordResetMessages
{
    public const INVALID_TARGET_EMAIL = 'invalid-reset-target@example.invalid';
    public const RESET_LINK_REQUEST_ACCEPTED = 'Se os dados informados estiverem corretos, as instrucoes para redefinicao de senha foram enviadas.';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::INVALID_TARGET_EMAIL,
            self::RESET_LINK_REQUEST_ACCEPTED,
        ];
    }
}
