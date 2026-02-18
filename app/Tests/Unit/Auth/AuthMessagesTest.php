<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthMessages;
use PHPUnit\Framework\TestCase;

class AuthMessagesTest extends TestCase
{
    public function testBuildsDynamicMessages(): void
    {
        $this->assertSame(
            'MFA temporariamente bloqueado. Tente novamente em 12 segundos.',
            AuthMessages::mfaBlockedTryAgain(12)
        );

        $this->assertSame(
            'MFA temporariamente bloqueado. Aguarde 30 segundos para reenviar.',
            AuthMessages::mfaBlockedResend(30)
        );

        $this->assertSame(
            'Acesso temporariamente bloqueado. Tente novamente em 9 segundos.',
            AuthMessages::loginTemporarilyBlocked(9)
        );
    }
}
