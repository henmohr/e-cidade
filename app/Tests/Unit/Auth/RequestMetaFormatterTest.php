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

    public function testBuildsNormalizedPathWithLeadingSlash(): void
    {
        $request = Request::create('web/test/path', 'GET');

        $this->assertSame('/web/test/path', RequestMetaFormatter::normalizedPath($request));
    }

    public function testTruncatesSessionPathToConfiguredLength(): void
    {
        $longPath = '/web/' . str_repeat('b', RequestMetaFormatter::SESSION_PATH_MAX_LENGTH + 50);
        $request = Request::create($longPath, 'GET');

        $result = RequestMetaFormatter::sessionPath($request);
        $this->assertSame(RequestMetaFormatter::SESSION_PATH_MAX_LENGTH, strlen($result));
    }
}
