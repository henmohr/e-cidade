<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventMetaKeys;
use PHPUnit\Framework\TestCase;

class AuthEventMetaKeysTest extends TestCase
{
    public function testAllKeysAreUniqueAndNonEmpty(): void
    {
        $keys = AuthEventMetaKeys::all();

        $this->assertNotEmpty($keys);
        $this->assertCount(count(array_unique($keys)), $keys);

        foreach ($keys as $key) {
            $this->assertNotSame('', trim((string) $key));
        }
    }
}
