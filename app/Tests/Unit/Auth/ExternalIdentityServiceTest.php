<?php

namespace App\Tests\Unit\Auth;

use App\Services\Auth\ExternalIdentityService;
use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\TestCase;

class ExternalIdentityServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
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

    public function testRejectsExpiredClaimsWindow(): void
    {
        $this->bootConfig([
            'external_identity' => [
                'enabled' => true,
                'allowed_providers' => 'govbr',
                'provider_secrets_json' => '{}',
                'allow_unsigned' => true,
                'enforce_claims_expiration' => true,
                'max_clock_skew_seconds' => 10,
                'enforce_nonce' => false,
            ],
        ]);

        $service = new ExternalIdentityService();
        $this->assertFalse($service->validateClaimsWindow([
            'expires_at' => '2020-01-01T00:00:00+00:00',
        ]));
    }

    public function testConsumesNonceOnlyOnce(): void
    {
        $this->bootConfig([
            'external_identity' => [
                'enabled' => true,
                'allowed_providers' => 'govbr',
                'provider_secrets_json' => '{}',
                'allow_unsigned' => true,
                'enforce_claims_expiration' => false,
                'max_clock_skew_seconds' => 60,
                'enforce_nonce' => true,
                'nonce_ttl_seconds' => 600,
            ],
            'cache.default' => 'array',
            'cache.stores.array' => ['driver' => 'array'],
        ]);

        $service = new ExternalIdentityService();
        $this->assertTrue($service->consumeNonce(['nonce' => 'nonce-unico']));
        $this->assertFalse($service->consumeNonce(['nonce' => 'nonce-unico']));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function bootConfig(array $config): void
    {
        $container = new Container();
        $container->instance('config', new Repository($config));
        $container->singleton('cache', static function ($app) {
            return new CacheManager($app);
        });
        $container->alias('cache', \Illuminate\Contracts\Cache\Factory::class);
        Container::setInstance($container);
        Facade::setFacadeApplication($container);
    }
}
