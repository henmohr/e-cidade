<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\PasswordResetMessages;
use PHPUnit\Framework\TestCase;

class PasswordResetMessagesTest extends TestCase
{
    public function testAllMessagesAreUniqueAndNonEmpty(): void
    {
        $messages = PasswordResetMessages::all();

        $this->assertNotEmpty($messages);
        $this->assertCount(count(array_unique($messages)), $messages);

        foreach ($messages as $message) {
            $this->assertNotSame('', trim((string) $message));
        }
    }
}
