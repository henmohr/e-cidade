<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\ExternalIdentityService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class ExternalIdentityServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testProviderAllowList(): void
    {
        $this->bootConfig([
            'external_identity' => [
                'enabled' => true,
                'allowed_providers' => 'govbr,google,a3',
                'provider_secrets_json' => '{}',
                'allow_unsigned' => false,
            ],
        ]);

        $service = new ExternalIdentityService();
        $this->assertTrue($service->isProviderAllowed('govbr'));
        $this->assertFalse($service->isProviderAllowed('facebook'));
    }

    public function testSignatureValidationWithProviderSecret(): void
    {
        $this->bootConfig([
            'external_identity' => [
                'enabled' => true,
                'allowed_providers' => 'govbr',
                'provider_secrets_json' => '{"govbr":"segredo-teste"}',
                'allow_unsigned' => false,
            ],
        ]);

        $service = new ExternalIdentityService();
        $payload = '{"provider":"govbr","cpf":"12345678901"}';
        $signature = hash_hmac('sha256', $payload, 'segredo-teste');

        $this->assertTrue($service->verifySignature('govbr', $payload, $signature));
        $this->assertFalse($service->verifySignature('govbr', $payload, 'assinatura-invalida'));
    }

    public function testUnsignedModeAllowsCallbackForPoc(): void
    {
        $this->bootConfig([
            'external_identity' => [
                'enabled' => true,
                'allowed_providers' => 'govbr',
                'provider_secrets_json' => '{}',
                'allow_unsigned' => true,
            ],
        ]);

        $service = new ExternalIdentityService();
        $this->assertTrue($service->verifySignature('govbr', '{"a":1}', null));
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
