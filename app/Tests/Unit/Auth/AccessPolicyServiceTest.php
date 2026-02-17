<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\AccessPolicyService;
use DateTimeImmutable;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;

class AccessPolicyServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testAllowsWhenPolicyIsDisabled(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => false,
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate($this->mockUser(10, false));

        $this->assertTrue($result['allowed']);
        $this->assertSame('disabled', $result['reason']);
    }

    public function testBlocksWhenOutsideAllowedWeekday(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => true,
                'timezone' => 'America/Sao_Paulo',
                'allow_admin_bypass' => false,
                'default_rule' => [
                    'allowed_weekdays' => '1,2,3,4,5',
                    'start_time' => '',
                    'end_time' => '',
                    'expires_at' => '',
                ],
                'group_rules_json' => '{}',
                'user_rules_json' => '{}',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate(
            $this->mockUser(11, false),
            new DateTimeImmutable('2026-02-15 10:00:00-03:00')
        );

        $this->assertFalse($result['allowed']);
        $this->assertSame('weekday', $result['reason']);
    }

    public function testBlocksWhenOutsideAllowedHourWindow(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => true,
                'timezone' => 'America/Sao_Paulo',
                'allow_admin_bypass' => false,
                'default_rule' => [
                    'allowed_weekdays' => '',
                    'start_time' => '08:00',
                    'end_time' => '18:00',
                    'expires_at' => '',
                ],
                'group_rules_json' => '{}',
                'user_rules_json' => '{}',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate(
            $this->mockUser(12, false),
            new DateTimeImmutable('2026-02-17 20:30:00-03:00')
        );

        $this->assertFalse($result['allowed']);
        $this->assertSame('hour', $result['reason']);
    }

    public function testAllowsInsideOvernightWindow(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => true,
                'timezone' => 'America/Sao_Paulo',
                'allow_admin_bypass' => false,
                'default_rule' => [
                    'allowed_weekdays' => '',
                    'start_time' => '22:00',
                    'end_time' => '06:00',
                    'expires_at' => '',
                ],
                'group_rules_json' => '{}',
                'user_rules_json' => '{}',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate(
            $this->mockUser(13, false),
            new DateTimeImmutable('2026-02-17 23:15:00-03:00')
        );

        $this->assertTrue($result['allowed']);
        $this->assertSame('allowed', $result['reason']);
    }

    public function testBlocksWhenUserRuleIsExpired(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => true,
                'timezone' => 'America/Sao_Paulo',
                'allow_admin_bypass' => false,
                'default_rule' => [
                    'allowed_weekdays' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'expires_at' => '',
                ],
                'group_rules_json' => '{}',
                'user_rules_json' => '{"77":{"expires_at":"2026-02-01 00:00:00-03:00"}}',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate(
            $this->mockUser(77, false),
            new DateTimeImmutable('2026-02-17 09:00:00-03:00')
        );

        $this->assertFalse($result['allowed']);
        $this->assertSame('expired', $result['reason']);
    }

    public function testAllowsAdminBypassWhenEnabled(): void
    {
        $this->bootConfig([
            'auth_access' => [
                'enabled' => true,
                'timezone' => 'America/Sao_Paulo',
                'allow_admin_bypass' => true,
                'default_rule' => [
                    'allowed_weekdays' => '1',
                    'start_time' => '10:00',
                    'end_time' => '10:05',
                    'expires_at' => '',
                ],
                'group_rules_json' => '{}',
                'user_rules_json' => '{}',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new AccessPolicyService();
        $result = $service->evaluate(
            $this->mockUser(99, true),
            new DateTimeImmutable('2026-02-17 22:00:00-03:00')
        );

        $this->assertTrue($result['allowed']);
        $this->assertSame('admin_bypass', $result['reason']);
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

    private function mockUser(int $id, bool $admin): User
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAuthIdentifier')->andReturn($id);
        $user->shouldReceive('isAdmin')->andReturn($admin);
        $user->usuext = 0;

        return $user;
    }
}
