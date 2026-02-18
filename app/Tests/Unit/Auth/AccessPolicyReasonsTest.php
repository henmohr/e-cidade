<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AccessPolicyReasons;
use PHPUnit\Framework\TestCase;

class AccessPolicyReasonsTest extends TestCase
{
    public function testAllReasonsAreUniqueAndNonEmpty(): void
    {
        $reasons = AccessPolicyReasons::all();

        $this->assertNotEmpty($reasons);
        $this->assertCount(count(array_unique($reasons)), $reasons);

        foreach ($reasons as $reason) {
            $this->assertNotSame('', trim((string) $reason));
        }
    }
}
