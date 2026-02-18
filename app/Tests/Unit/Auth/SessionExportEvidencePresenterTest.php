<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\SessionExportEvidencePresenter;
use PHPUnit\Framework\TestCase;

class SessionExportEvidencePresenterTest extends TestCase
{
    public function testBuildsNotFoundPayload(): void
    {
        $presenter = new SessionExportEvidencePresenter();
        $payload = $presenter->notFound();

        $this->assertFalse($payload['verified']);
        $this->assertSame('Hash nao encontrado nos eventos recentes de exportacao.', $payload['message']);
    }

    public function testBuildsVerifiedPayload(): void
    {
        $presenter = new SessionExportEvidencePresenter();
        $payload = $presenter->verified('ABCDEF', [
            'type' => 'sessions_export_csv',
            'timestamp' => '2026-02-18T12:00:00Z',
            'request_id' => 'req-42',
            'row_count' => 7,
        ]);

        $this->assertTrue($payload['verified']);
        $this->assertSame('abcdef', $payload['hash']);
        $this->assertSame('sessions_export_csv', $payload['event_type']);
        $this->assertSame('req-42', $payload['request_id']);
        $this->assertSame(7, $payload['row_count']);
    }
}
