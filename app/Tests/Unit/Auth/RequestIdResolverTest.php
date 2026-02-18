<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\RequestIdResolver;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestIdResolverTest extends TestCase
{
    public function testResolvesFromAttributeBeforeHeader(): void
    {
        $request = Request::create('/web/test', 'GET', [], [], [], [
            'HTTP_X_REQUEST_ID' => 'req-header',
        ]);
        $request->attributes->set('request_id', 'req-attribute');

        $this->assertSame('req-attribute', RequestIdResolver::resolve($request));
    }

    public function testFallsBackToHeaderWhenAttributeIsMissing(): void
    {
        $request = Request::create('/web/test', 'GET', [], [], [], [
            'HTTP_X_REQUEST_ID' => 'req-header',
        ]);

        $this->assertSame('req-header', RequestIdResolver::resolve($request));
    }
}
