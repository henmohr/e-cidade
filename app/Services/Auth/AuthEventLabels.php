<?php

namespace App\Services\Auth;

final class AuthEventLabels
{
    /**
     * @return array<string, string>
     */
    public static function byType(): array
    {
        return [
            AuthEventTypes::LOGIN_SUCCESS => 'Login com sucesso',
            AuthEventTypes::LOGIN_FAILED => 'Falha de login',
            AuthEventTypes::LOGIN_EXTERNAL_SUCCESS => 'Login externo com sucesso',
            AuthEventTypes::LOGOUT => 'Logout',
            AuthEventTypes::MFA_VERIFY_SUCCESS => 'MFA validado',
            AuthEventTypes::MFA_VERIFY_FAILED => 'Falha na validacao MFA',
            AuthEventTypes::MFA_VERIFY_BLOCKED => 'MFA bloqueado',
            AuthEventTypes::MFA_CODE_RESENT => 'Reenvio de codigo MFA',
            AuthEventTypes::SESSION_REVOKED => 'Sessao revogada',
            AuthEventTypes::SESSION_REVOKE_OTHERS => 'Sessoes anteriores revogadas',
            AuthEventTypes::BACKUP_LINK_GENERATED => 'Link de backup gerado',
            AuthEventTypes::BACKUP_DOWNLOAD_EXECUTED => 'Download de backup',
            AuthEventTypes::SESSIONS_EXPORT_CSV => 'Exportacao CSV de eventos',
        ];
    }
}
