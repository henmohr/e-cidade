<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventPresenter;
use App\Services\Auth\AuthEventMetaKeys;
use App\Services\Auth\AuthEventTypes;
use PHPUnit\Framework\TestCase;

class AuthEventPresenterTest extends TestCase
{
    public function testBuildsFriendlyLabelAndDetails(): void
    {
        $presenter = new AuthEventPresenter();

        $event = [
            AuthEventMetaKeys::TYPE => AuthEventTypes::LOGIN_EXTERNAL_SUCCESS,
            AuthEventMetaKeys::PROVIDER => 'govbr',
        ];

        $this->assertSame('Login externo com sucesso', $presenter->typeLabel($event));
        $this->assertSame('provider=govbr', $presenter->details($event));
    }

    public function testComposesDetailsForSessionAndBackupEvents(): void
    {
        $presenter = new AuthEventPresenter();

        $event = [
            AuthEventMetaKeys::TYPE => AuthEventTypes::SESSION_REVOKE_OTHERS,
            AuthEventMetaKeys::REVOKED_COUNT => 3,
            AuthEventMetaKeys::TARGET_SESSION_ID => 'abc123xyz',
            AuthEventMetaKeys::TIER => 'archive',
            AuthEventMetaKeys::FILE => 'backup_2026_02_18.sql',
            AuthEventMetaKeys::BLOCKED_SECONDS => 120,
        ];

        $details = $presenter->details($event);
        $this->assertStringContainsString('revoked=3', $details);
        $this->assertStringContainsString('target=abc123xyz', $details);
        $this->assertStringContainsString('tier=archive', $details);
        $this->assertStringContainsString('file=backup_2026_02_18.sql', $details);
        $this->assertStringContainsString('blocked=120s', $details);
    }

    public function testComposesDetailsForCsvExportEvent(): void
    {
        $presenter = new AuthEventPresenter();

        $event = [
            AuthEventMetaKeys::TYPE => AuthEventTypes::SESSIONS_EXPORT_CSV,
            AuthEventMetaKeys::ROW_COUNT => 12,
            AuthEventMetaKeys::EXPORT_SHA256 => 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890',
        ];

        $this->assertSame('Exportacao CSV de eventos', $presenter->typeLabel($event));
        $details = $presenter->details($event);
        $this->assertStringContainsString('rows=12', $details);
        $this->assertStringContainsString('sha256=abcdef1234567890...', $details);
    }
}
