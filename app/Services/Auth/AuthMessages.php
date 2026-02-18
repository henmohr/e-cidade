<?php

namespace App\Services\Auth;

final class AuthMessages
{
    public const SESSION_NOT_FOUND = 'Sessao nao encontrada ou ja encerrada.';
    public const SESSION_REVOKED_SUCCESS = 'Sessao encerrada com sucesso.';

    public const MFA_INVALID_OR_EXPIRED = 'Código MFA inválido ou expirado.';
    public const MFA_RESEND_SUCCESS = 'Novo código MFA enviado.';

    public const EXTERNAL_DISABLED = 'Integracao de identidade externa desabilitada.';
    public const EXTERNAL_PROVIDER_NOT_ALLOWED = 'Provedor externo nao permitido.';
    public const EXTERNAL_INVALID_SIGNATURE = 'Assinatura invalida para callback externo.';
    public const EXTERNAL_INVALID_PAYLOAD = 'Payload de identidade invalido.';
    public const EXTERNAL_EXPIRED_CLAIMS = 'Claims expirados ou invalidos para login externo.';
    public const EXTERNAL_INVALID_NONCE = 'Nonce invalido ou ja utilizado.';
    public const EXTERNAL_USER_NOT_FOUND = 'Usuario nao encontrado para o identificador recebido.';
    public const EXTERNAL_LOGIN_SUCCESS = 'Login externo realizado com sucesso.';

    public const BACKUP_INVALID_FILE_NAME = 'Nome de arquivo invalido.';
    public const BACKUP_FILE_NOT_FOUND = 'Arquivo nao encontrado.';
    public const EXPORT_HASH_NOT_FOUND = 'Hash nao encontrado nos eventos recentes de exportacao.';

    public static function mfaBlockedTryAgain(int $seconds): string
    {
        return sprintf('MFA temporariamente bloqueado. Tente novamente em %d segundos.', max(1, $seconds));
    }

    public static function mfaBlockedResend(int $seconds): string
    {
        return sprintf('MFA temporariamente bloqueado. Aguarde %d segundos para reenviar.', max(1, $seconds));
    }

    public static function loginTemporarilyBlocked(int $seconds): string
    {
        return sprintf('Acesso temporariamente bloqueado. Tente novamente em %d segundos.', max(1, $seconds));
    }
}
