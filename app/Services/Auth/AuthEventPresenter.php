<?php

namespace App\Services\Auth;

class AuthEventPresenter
{
    /**
     * @param array<string, mixed> $event
     */
    public function typeLabel(array $event): string
    {
        $type = strtolower(trim((string) ($event['type'] ?? '')));

        $labels = [
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

        return $labels[$type] ?? ($type !== '' ? $type : '-');
    }

    /**
     * @param array<string, mixed> $event
     */
    public function details(array $event): string
    {
        $parts = [];

        if (!empty($event['provider'])) {
            $parts[] = 'provider=' . strtolower(trim((string) $event['provider']));
        }

        if (!empty($event['revoked_count'])) {
            $parts[] = 'revoked=' . (int) $event['revoked_count'];
        }

        if (!empty($event['target_session_id'])) {
            $parts[] = 'target=' . substr((string) $event['target_session_id'], 0, 40);
        }

        if (!empty($event['tier'])) {
            $parts[] = 'tier=' . strtolower(trim((string) $event['tier']));
        }

        if (!empty($event['file'])) {
            $parts[] = 'file=' . substr((string) $event['file'], 0, 80);
        }

        if (!empty($event['blocked_seconds'])) {
            $parts[] = 'blocked=' . (int) $event['blocked_seconds'] . 's';
        }

        if (isset($event['row_count'])) {
            $parts[] = 'rows=' . (int) $event['row_count'];
        }

        if (!empty($event['export_sha256'])) {
            $parts[] = 'sha256=' . substr((string) $event['export_sha256'], 0, 16) . '...';
        }

        if (empty($parts)) {
            return '-';
        }

        return implode(' | ', $parts);
    }
}
