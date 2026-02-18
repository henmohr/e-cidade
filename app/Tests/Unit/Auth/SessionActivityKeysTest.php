<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\SessionActivityKeys;
use PHPUnit\Framework\TestCase;

class SessionActivityKeysTest extends TestCase
{
    public function testAllKeysAreUniqueAndNonEmpty(): void
    {
        $keys = SessionActivityKeys::all();

        $this->assertNotEmpty($keys);
        $this->assertCount(count(array_unique($keys)), $keys);

        foreach ($keys as $key) {
            $this->assertNotSame('', trim((string) $key));
        }
    }
}
