<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Services\Auth\MfaService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;

class MfaServicePolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testReturnsFalseWhenMfaDisabled(): void
    {
        $this->bootConfig([
            'mfa' => [
                'enabled' => false,
            ],
        ]);

        $service = new MfaService();
        $this->assertFalse($service->requiresMfa($this->mockUser(1, true)));
    }

    public function testSupportsExplicitRequiredUsers(): void
    {
        $this->bootConfig([
            'mfa' => [
                'enabled' => true,
                'admins_only' => true,
                'allow_admin_bypass' => false,
                'required_users' => '7,8,9',
                'required_groups' => '',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new MfaService();
        $this->assertTrue($service->requiresMfa($this->mockUser(8, false)));
    }

    public function testSupportsRequiredGroupsWithUserMapping(): void
    {
        $this->bootConfig([
            'mfa' => [
                'enabled' => true,
                'admins_only' => true,
                'allow_admin_bypass' => false,
                'required_users' => '',
                'required_groups' => 'financeiro,licitacao',
                'user_groups_json' => '{"33":["financeiro"]}',
            ],
        ]);

        $service = new MfaService();
        $this->assertTrue($service->requiresMfa($this->mockUser(33, false)));
        $this->assertFalse($service->requiresMfa($this->mockUser(44, false)));
    }

    public function testAdminBypassDisablesMfaForAdmin(): void
    {
        $this->bootConfig([
            'mfa' => [
                'enabled' => true,
                'admins_only' => true,
                'allow_admin_bypass' => true,
                'required_users' => '',
                'required_groups' => '',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new MfaService();
        $this->assertFalse($service->requiresMfa($this->mockUser(2, true)));
    }

    public function testFallbackToAdminsOnlyToggle(): void
    {
        $this->bootConfig([
            'mfa' => [
                'enabled' => true,
                'admins_only' => true,
                'allow_admin_bypass' => false,
                'required_users' => '',
                'required_groups' => '',
                'user_groups_json' => '{}',
            ],
        ]);

        $service = new MfaService();
        $this->assertTrue($service->requiresMfa($this->mockUser(3, true)));
        $this->assertFalse($service->requiresMfa($this->mockUser(4, false)));
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
