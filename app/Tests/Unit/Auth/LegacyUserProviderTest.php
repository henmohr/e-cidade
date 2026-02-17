<?php

namespace App\Tests\Unit\Auth;

use App\Models\User;
use App\Providers\Auth\LegacyUserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Mockery;
use PHPUnit\Framework\TestCase;

class LegacyUserProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidateCredentialsWithModernHash()
    {
        $hasher = Mockery::mock(Hasher::class);
        $provider = new LegacyUserProvider($hasher);

        $user = Mockery::mock(User::class)->makePartial();
        $user->login = 'usuario_teste';
        $user->shouldReceive('isActive')->once()->andReturn(true);
        $user->shouldReceive('getAuthPassword')->once()->andReturn('$2y$10$abcdefghijklmnopqrstuv123456789012345678901234567890');

        $hasher->shouldReceive('check')
            ->once()
            ->with('senha123', '$2y$10$abcdefghijklmnopqrstuv123456789012345678901234567890')
            ->andReturn(true);

        $this->assertTrue($provider->validateCredentials($user, ['senha' => 'senha123']));
    }

    public function testValidateCredentialsWithLegacyHashRehashesPassword()
    {
        $hasher = Mockery::mock(Hasher::class);
        $provider = new LegacyUserProvider($hasher);

        $user = Mockery::mock(User::class)->makePartial();
        $user->login = 'usuario_legado';
        $user->shouldReceive('isActive')->once()->andReturn(true);
        $user->shouldReceive('getAuthPassword')->andReturn(md5(sha1('senha-antiga')));
        $user->shouldReceive('save')->once()->andReturn(true);

        $hasher->shouldReceive('make')->once()->with('senha-antiga')->andReturn('$2y$10$rehashedpasswordvalue');

        $this->assertTrue($provider->validateCredentials($user, ['senha' => 'senha-antiga']));
    }

    public function testValidateCredentialsRejectsUnknownHashFormat()
    {
        $hasher = Mockery::mock(Hasher::class);
        $provider = new LegacyUserProvider($hasher);

        $user = Mockery::mock(User::class)->makePartial();
        $user->login = 'usuario_invalido';
        $user->shouldReceive('isActive')->once()->andReturn(true);
        $user->shouldReceive('getAuthPassword')->once()->andReturn('hash_sem_formato_valido');

        $hasher->shouldNotReceive('check');
        $hasher->shouldNotReceive('make');

        $this->assertFalse($provider->validateCredentials($user, ['senha' => 'qualquer']));
    }
}
