<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\AuthEventService;
use App\Services\Auth\AuthEventTypes;
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
        $service->registerCustomEvent($request, $user, AuthEventTypes::SESSION_REVOKED, ['target_session_id' => 'abc123']);
        $service->registerCustomEvent($request, $user, AuthEventTypes::SESSIONS_EXPORT_CSV, [
            'row_count' => 2,
            'export_sha256' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        ]);
        $service->registerLogout($request, $user);

        $events = $service->listRecentEventsForUser($user);
        $types = array_map(static function (array $event): string {
            return (string) ($event['type'] ?? '');
        }, $events);

        $this->assertContains(AuthEventTypes::LOGIN_EXTERNAL_SUCCESS, $types);
        $this->assertContains(AuthEventTypes::SESSION_REVOKED, $types);
        $this->assertContains(AuthEventTypes::LOGOUT, $types);
        $this->assertSame('req-test-123', (string) ($events[0]['request_id'] ?? ''));

        $filteredByType = $service->listRecentEventsForUserFiltered($user, AuthEventTypes::LOGOUT, null, 10);
        $this->assertCount(1, $filteredByType);
        $this->assertSame(AuthEventTypes::LOGOUT, (string) ($filteredByType[0]['type'] ?? ''));

        $filteredByRequest = $service->listRecentEventsForUserFiltered($user, null, 'req-test-123', 10);
        $this->assertNotEmpty($filteredByRequest);

        $foundExport = $service->findRecentExportEventByHash(
            $user,
            'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
        );
        $this->assertNotNull($foundExport);
        $this->assertSame(AuthEventTypes::SESSIONS_EXPORT_CSV, (string) ($foundExport['type'] ?? ''));
    }

    public function testFilteredListNormalizesLimitBoundaries(): void
    {
        $this->bootContainer();

        $service = new AuthEventService();
        $user = $this->mockUser(51, 'maria');
        $request = Request::create('/web/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'PHPUnit',
            'HTTP_X_REQUEST_ID' => 'req-limit-1',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $service->registerCustomEvent($request, $user, AuthEventTypes::LOGIN_SUCCESS, ['index' => $i]);
        }

        $lowerBound = $service->listRecentEventsForUserFiltered($user, null, null, 0);
        $upperBound = $service->listRecentEventsForUserFiltered($user, null, null, 999);

        $this->assertCount(1, $lowerBound);
        $this->assertCount(5, $upperBound);
    }

    public function testUsesRequestIdAttributeOverHeader(): void
    {
        $this->bootContainer();

        $service = new AuthEventService();
        $user = $this->mockUser(52, 'carla');
        $request = Request::create('/web/login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'PHPUnit',
            'HTTP_X_REQUEST_ID' => 'req-header',
        ]);
        $request->attributes->set('request_id', 'req-attribute');

        $service->registerSuccess($request, $user);
        $events = $service->listRecentEventsForUser($user);

        $this->assertSame('req-attribute', (string) ($events[0]['request_id'] ?? ''));
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
