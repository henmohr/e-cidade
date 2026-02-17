<?php

namespace App\Providers\Auth;

use App\Helpers\UserCache;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\ValidationException;

class LegacyUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var Hasher
     */
    protected Hasher $hasher;

    /**
     * Create a new database user provider.
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @inheritDoc
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        return UserCache::user($identifier);
    }

    /**
     * @inheritDoc
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = UserCache::user($identifier);

        if (!$model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    /**
     * @inheritDoc
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $user->saveOrFail();
    }

    /**
     * @inheritDoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        $rawLogin = trim((string) ($credentials['login'] ?? ''));
        if ($rawLogin === '') {
            return null;
        }

        $normalizedCpf = preg_replace('/\D+/', '', $rawLogin);

        $query = User::query()
            ->with('cgm')
            ->where('login', $rawLogin);

        if (!empty($normalizedCpf)) {
            $query->orWhereHas('cgm', function ($subQuery) use ($normalizedCpf) {
                $subQuery->whereRaw("regexp_replace(z01_cgccpf, '[^0-9]', '', 'g') = ?", [$normalizedCpf]);
            });
        }

        return $query->first();
    }

    /**
     * @inheritDoc
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                $user->login => 'Usuario inativo',
            ]);
        }

        $storedHash = (string) $user->getAuthPassword();

        if ($this->isModernHash($storedHash)) {
            return $this->hasher->check($credentials['senha'], $storedHash);
        }

        if (!$this->isLegacyHash($storedHash)) {
            return false;
        }

        return $this->validateLegacyCredentials($user, $credentials);
    }

    /**
     * Validate legacy credentials and rehash if correct.
     *
     * @param User $user
     * @param array $credentials
     * @return bool
     */
    public function validateLegacyCredentials(User $user, array $credentials): bool
    {
        $plain = $credentials['senha'];
        $storedHash = (string) $user->getAuthPassword();

        if (!$this->isLegacyHash($storedHash)) {
            return false;
        }

        if (md5(sha1($plain)) !== $storedHash) {
            return false;
        }

        $this->rehashPassword($user, $plain);

        return true;
    }

    /**
     * Rehash a legacy password that uses MD5.
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    public function rehashPassword(User $user, string $password)
    {
        $user->senha = $this->hasher->make($password);
        $user->save();
    }

    private function isModernHash(string $hash): bool
    {
        return str_starts_with($hash, '$2y$')
            || str_starts_with($hash, '$2a$')
            || str_starts_with($hash, '$argon2i$')
            || str_starts_with($hash, '$argon2id$');
    }

    private function isLegacyHash(string $hash): bool
    {
        return (bool) preg_match('/^[a-f0-9]{32}$/i', $hash);
    }
}
