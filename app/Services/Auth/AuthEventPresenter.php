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
            'login_success' => 'Login com sucesso',
            'login_failed' => 'Falha de login',
            'login_external_success' => 'Login externo com sucesso',
            'logout' => 'Logout',
            'mfa_verify_success' => 'MFA validado',
            'mfa_verify_failed' => 'Falha na validacao MFA',
            'mfa_verify_blocked' => 'MFA bloqueado',
            'mfa_code_resent' => 'Reenvio de codigo MFA',
            'session_revoked' => 'Sessao revogada',
            'session_revoke_others' => 'Sessoes anteriores revogadas',
            'backup_link_generated' => 'Link de backup gerado',
            'backup_download_executed' => 'Download de backup',
            'sessions_export_csv' => 'Exportacao CSV de eventos',
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
