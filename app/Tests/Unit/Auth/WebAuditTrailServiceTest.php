<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\WebAuditTrailService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

class WebAuditTrailServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testShouldSkipConfiguredPath(): void
    {
        $this->bootConfig([
            'web_audit' => [
                'enabled' => true,
                'exclude_paths' => ['web/mfa/challenge', 'web/mfa/resend'],
            ],
        ]);

        $service = new WebAuditTrailService();
        $this->assertTrue($service->shouldSkipPath('/web/mfa/challenge'));
        $this->assertFalse($service->shouldSkipPath('/web/welcome'));
    }

    public function testBuildContextMasksSensitiveInputKeys(): void
    {
        $this->bootConfig([
            'web_audit' => [
                'enabled' => true,
                'include_query' => true,
                'include_input_keys' => true,
                'sensitive_keys' => ['senha', 'token'],
                'exclude_paths' => [],
            ],
        ]);

        $request = Request::create('/web/backup/link?foo=1', 'POST', [
            'senha' => 'segredo',
            'token' => 'abc',
            'nome' => 'Maria',
            'cpf' => '12345678901',
        ]);

        $request->setRouteResolver(static function () {
            return null;
        });

        $service = new WebAuditTrailService();
        $context = $service->buildContext($request, 200, 42);

        $this->assertSame('/web/backup/link', $context['path']);
        $this->assertSame(200, $context['status']);
        $this->assertSame(42, $context['duration_ms']);
        $this->assertContains('foo', $context['query_keys']);
        $this->assertContains('nome', $context['input_keys']);
        $this->assertNotContains('senha', $context['input_keys']);
        $this->assertNotContains('token', $context['input_keys']);
    }

    public function testBuildContextUsesResolvedRequestId(): void
    {
        $this->bootConfig([
            'web_audit' => [
                'enabled' => true,
                'include_query' => false,
                'include_input_keys' => false,
                'sensitive_keys' => [],
                'exclude_paths' => [],
            ],
        ]);

        $request = Request::create('/web/audit', 'GET', [], [], [], [
            'HTTP_X_REQUEST_ID' => 'req-header-77',
        ]);
        $request->attributes->set('request_id', 'req-attr-88');
        $request->setRouteResolver(static function () {
            return null;
        });

        $service = new WebAuditTrailService();
        $context = $service->buildContext($request, 204, 10);

        $this->assertSame('req-attr-88', $context['request_id']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function bootConfig(array $config): void
    {
        $container = new Container();
        $container->instance('config', new Repository($config));
        Container::setInstance($container);
    }
}
