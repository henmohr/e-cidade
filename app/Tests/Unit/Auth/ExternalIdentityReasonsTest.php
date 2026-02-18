<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\ExternalIdentityReasons;
use PHPUnit\Framework\TestCase;

class ExternalIdentityReasonsTest extends TestCase
{
    public function testAllReasonsAreUniqueAndNonEmpty(): void
    {
        $reasons = ExternalIdentityReasons::all();

        $this->assertNotEmpty($reasons);
        $this->assertCount(count(array_unique($reasons)), $reasons);

        foreach ($reasons as $reason) {
            $this->assertNotSame('', trim((string) $reason));
        }
    }
}
