<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\AuthEventService;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthEventServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testRegistersExternalSuccessAndLogoutEvents(): void
    {
        $this->bootContainer();

        $service = new AuthEventService();
        $user = $this->mockUser(50, 'joao');

        $request = Request::create('/web/idp/callback', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'PHPUnit',
            'HTTP_X_REQUEST_ID' => 'req-test-123',
        ]);

        $service->registerExternalSuccess($request, $user, 'govbr');
        $service->registerCustomEvent($request, $user, 'session_revoked', ['target_session_id' => 'abc123']);
        $service->registerLogout($request, $user);

        $events = $service->listRecentEventsForUser($user);
        $types = array_map(static function (array $event): string {
            return (string) ($event['type'] ?? '');
        }, $events);

        $this->assertContains('login_external_success', $types);
        $this->assertContains('session_revoked', $types);
        $this->assertContains('logout', $types);
        $this->assertSame('req-test-123', (string) ($events[0]['request_id'] ?? ''));
    }

    private function bootContainer(): void
    {
        $container = new Container();
        $container->instance('config', new Repository([
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array'],
            'auth.auth_events.retention_days' => 7,
        ]));

        $container->singleton('cache', static function ($app) {
            return new CacheManager($app);
        });
        $container->alias('cache', \Illuminate\Contracts\Cache\Factory::class);

        Container::setInstance($container);
        Facade::setFacadeApplication($container);
    }

    private function mockUser(int $id, string $login): User
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->login = $login;
        $user->cpf = null;
        $user->shouldReceive('getAuthIdentifier')->andReturn($id);

        return $user;
    }
}
