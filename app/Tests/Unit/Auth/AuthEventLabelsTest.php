<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\AuthEventLabels;
use App\Services\Auth\AuthEventTypes;
use PHPUnit\Framework\TestCase;

class AuthEventLabelsTest extends TestCase
{
    public function testAllAuthEventTypesHaveLabels(): void
    {
        $labels = AuthEventLabels::byType();
        $types = AuthEventTypes::all();

        $this->assertNotEmpty($labels);
        $this->assertCount(count(array_unique(array_keys($labels))), array_keys($labels));
        $this->assertCount(count(array_unique(array_values($labels))), array_values($labels));

        foreach ($types as $type) {
            $this->assertArrayHasKey($type, $labels);
            $this->assertNotSame('', trim((string) $labels[$type]));
        }
    }
}
