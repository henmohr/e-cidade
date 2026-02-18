<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\ExportHash;
use PHPUnit\Framework\TestCase;

class ExportHashTest extends TestCase
{
    public function testNormalizeLowercasesAndTrims(): void
    {
        $value = '  ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890  ';

        $this->assertSame(
            'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890',
            ExportHash::normalize($value)
        );
    }

    public function testValidatesSha256Hex(): void
    {
        $valid = 'abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890';
        $invalid = 'not-a-hash';

        $this->assertTrue(ExportHash::isValid($valid));
        $this->assertFalse(ExportHash::isValid($invalid));
    }
}
