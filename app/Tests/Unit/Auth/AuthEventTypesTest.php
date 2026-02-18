<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventTypes;
use PHPUnit\Framework\TestCase;

class AuthEventTypesTest extends TestCase
{
    public function testAllEventTypesAreUniqueAndNonEmpty(): void
    {
        $types = AuthEventTypes::all();

        $this->assertNotEmpty($types);
        $this->assertCount(count(array_unique($types)), $types);

        foreach ($types as $type) {
            $this->assertNotSame('', trim((string) $type));
        }
    }
}
