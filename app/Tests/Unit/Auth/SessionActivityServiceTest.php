<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\SessionActivityKeys;
use App\Services\Auth\SessionActivityService;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Facade;
use Mockery;
use PHPUnit\Framework\TestCase;

class SessionActivityServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testRevokeOtherSessionsKeepsCurrentSessionOnly(): void
    {
        $this->bootContainer();

        $service = new SessionActivityService();
        $user = $this->mockUser(20);
        $cacheKey = 'auth:sessions:user:20';

        Cache::put($cacheKey, [
            'keep-session' => [SessionActivityKeys::SESSION_ID => 'keep-session'],
            'old-session-1' => [SessionActivityKeys::SESSION_ID => 'old-session-1'],
            'old-session-2' => [SessionActivityKeys::SESSION_ID => 'old-session-2'],
        ], now()->addMinutes(120));

        $revoked = $service->revokeOtherSessions($user, 'keep-session');
        $this->assertSame(2, $revoked);

        $stored = Cache::get($cacheKey, []);
        $this->assertArrayHasKey('keep-session', $stored);
        $this->assertArrayNotHasKey('old-session-1', $stored);
        $this->assertArrayNotHasKey('old-session-2', $stored);
        $this->assertTrue($service->isRevoked('old-session-1'));
        $this->assertTrue($service->isRevoked('old-session-2'));
    }

    private function bootContainer(): void
    {
        $container = new Container();
        $container->instance('config', new Repository([
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array'],
            'session.lifetime' => 120,
        ]));
        $container->singleton('cache', static function ($app) {
            return new CacheManager($app);
        });
        $container->alias('cache', \Illuminate\Contracts\Cache\Factory::class);

        Container::setInstance($container);
        Facade::setFacadeApplication($container);
    }

    private function mockUser(int $id): User
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAuthIdentifier')->andReturn($id);

        return $user;
    }
}
