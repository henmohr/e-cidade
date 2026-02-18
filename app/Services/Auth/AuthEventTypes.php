<?php

namespace App\Services\Auth;

final class AuthEventTypes
{
    public const LOGIN_SUCCESS = 'login_success';
    public const LOGIN_FAILED = 'login_failed';
    public const LOGIN_EXTERNAL_SUCCESS = 'login_external_success';
    public const LOGOUT = 'logout';

    public const MFA_VERIFY_SUCCESS = 'mfa_verify_success';
    public const MFA_VERIFY_FAILED = 'mfa_verify_failed';
    public const MFA_VERIFY_BLOCKED = 'mfa_verify_blocked';
    public const MFA_CODE_RESENT = 'mfa_code_resent';

    public const SESSION_REVOKED = 'session_revoked';
    public const SESSION_REVOKE_OTHERS = 'session_revoke_others';

    public const BACKUP_LINK_GENERATED = 'backup_link_generated';
    public const BACKUP_DOWNLOAD_EXECUTED = 'backup_download_executed';

    public const SESSIONS_EXPORT_CSV = 'sessions_export_csv';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::LOGIN_SUCCESS,
            self::LOGIN_FAILED,
            self::LOGIN_EXTERNAL_SUCCESS,
            self::LOGOUT,
            self::MFA_VERIFY_SUCCESS,
            self::MFA_VERIFY_FAILED,
            self::MFA_VERIFY_BLOCKED,
            self::MFA_CODE_RESENT,
            self::SESSION_REVOKED,
            self::SESSION_REVOKE_OTHERS,
            self::BACKUP_LINK_GENERATED,
            self::BACKUP_DOWNLOAD_EXECUTED,
            self::SESSIONS_EXPORT_CSV,
        ];
    }
}
