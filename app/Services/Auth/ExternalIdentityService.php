<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ExternalIdentityService
{
    public function isEnabled(): bool
    {
        return (bool) config('external_identity.enabled', false);
    }

    public function isProviderAllowed(string $provider): bool
    {
        $provider = trim(strtolower($provider));
        if ($provider === '') {
            return false;
        }

        return in_array($provider, $this->allowedProviders(), true);
    }

    public function verifySignature(string $provider, string $payload, ?string $signature): bool
    {
        if ((bool) config('external_identity.allow_unsigned', false)) {
            return true;
        }

        if (!$this->isProviderAllowed($provider)) {
            return false;
        }

        $secret = $this->providerSecret($provider);
        if ($secret === '' || empty($signature)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, trim($signature));
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function validateClaimsWindow(array $claims): bool
    {
        if (!(bool) config('external_identity.enforce_claims_expiration', true)) {
            return true;
        }

        $expiresAtRaw = trim((string) ($claims['expires_at'] ?? ''));
        if ($expiresAtRaw === '') {
            return false;
        }

        try {
            $expiresAt = new \DateTimeImmutable($expiresAtRaw);
        } catch (\Throwable $e) {
            return false;
        }

        $now = new \DateTimeImmutable('now', $expiresAt->getTimezone());
        $skew = max(0, (int) config('external_identity.max_clock_skew_seconds', 60));

        return $now->getTimestamp() <= ($expiresAt->getTimestamp() + $skew);
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function consumeNonce(array $claims): bool
    {
        if (!(bool) config('external_identity.enforce_nonce', true)) {
            return true;
        }

        $nonce = trim((string) ($claims['nonce'] ?? ''));
        if ($nonce === '') {
            return false;
        }

        $key = 'auth:external:nonce:' . sha1($nonce);
        if (Cache::has($key)) {
            return false;
        }

        $ttl = max(60, (int) config('external_identity.nonce_ttl_seconds', 600));
        Cache::put($key, 1, now()->addSeconds($ttl));

        return true;
    }

    /**
     * @param array<string, mixed> $claims
     */
    public function resolveUser(array $claims): ?User
    {
        $rawCpf = (string) ($claims['cpf'] ?? '');
        $normalizedCpf = preg_replace('/\D+/', '', $rawCpf);
        $rawLogin = trim((string) ($claims['login'] ?? ''));

        $query = User::query();
        $hasCriteria = false;

        if ($normalizedCpf !== '') {
            $query->whereHas('cgm', function ($subQuery) use ($normalizedCpf) {
                $subQuery->whereRaw("regexp_replace(z01_cgccpf, '[^0-9]', '', 'g') = ?", [$normalizedCpf]);
            });
            $hasCriteria = true;
        }

        if ($rawLogin !== '') {
            if ($hasCriteria) {
                $query->orWhere('login', $rawLogin);
            } else {
                $query->where('login', $rawLogin);
                $hasCriteria = true;
            }
        }

        if (!$hasCriteria) {
            return null;
        }

        /** @var User|null $user */
        $user = $query->first();
        if (!$user || !$user->isActive()) {
            return null;
        }

        return $user;
    }

    /**
     * @return array<int, string>
     */
    public function allowedProviders(): array
    {
        $value = (string) config('external_identity.allowed_providers', 'govbr,google,a1,a3');
        $parts = array_map('trim', explode(',', strtolower($value)));
        $parts = array_filter($parts, static function (string $part): bool {
            return $part !== '';
        });

        return array_values(array_unique($parts));
    }

    private function providerSecret(string $provider): string
    {
        $provider = trim(strtolower($provider));
        if ($provider === '') {
            return '';
        }

        $mapping = json_decode((string) config('external_identity.provider_secrets_json', '{}'), true);
        if (!is_array($mapping)) {
            return '';
        }

        $secret = $mapping[$provider] ?? '';
        return is_string($secret) ? trim($secret) : '';
    }
}
