<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventPresenter;
use PHPUnit\Framework\TestCase;

class AuthEventPresenterTest extends TestCase
{
    public function testBuildsFriendlyLabelAndDetails(): void
    {
        $presenter = new AuthEventPresenter();

        $event = [
            'type' => 'login_external_success',
            'provider' => 'govbr',
        ];

        $this->assertSame('Login externo com sucesso', $presenter->typeLabel($event));
        $this->assertSame('provider=govbr', $presenter->details($event));
    }

    public function testComposesDetailsForSessionAndBackupEvents(): void
    {
        $presenter = new AuthEventPresenter();

        $event = [
            'type' => 'session_revoke_others',
            'revoked_count' => 3,
            'target_session_id' => 'abc123xyz',
            'tier' => 'archive',
            'file' => 'backup_2026_02_18.sql',
            'blocked_seconds' => 120,
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
            'type' => 'sessions_export_csv',
            'row_count' => 12,
            'export_sha256' => 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890',
        ];

        $this->assertSame('Exportacao CSV de eventos', $presenter->typeLabel($event));
        $details = $presenter->details($event);
        $this->assertStringContainsString('rows=12', $details);
        $this->assertStringContainsString('sha256=abcdef1234567890...', $details);
    }
}
