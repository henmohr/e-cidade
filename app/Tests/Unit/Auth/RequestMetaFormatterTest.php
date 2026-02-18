<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\RequestMetaFormatter;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestMetaFormatterTest extends TestCase
{
    public function testTruncatesUserAgentToConfiguredLength(): void
    {
        $request = Request::create('/web/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => str_repeat('a', RequestMetaFormatter::USER_AGENT_MAX_LENGTH + 50),
        ]);

        $result = RequestMetaFormatter::userAgent($request);

        $this->assertSame(RequestMetaFormatter::USER_AGENT_MAX_LENGTH, strlen($result));
    }
}
