<?php

namespace App\Services\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MfaService
{
    private const SESSION_USER_ID = 'auth.mfa.user_id';
    private const SESSION_CODE_HASH = 'auth.mfa.code_hash';
    private const SESSION_EXPIRES_AT = 'auth.mfa.expires_at';
    private const SESSION_VERIFIED_USER_ID = 'auth.mfa.verified_user_id';
    private const SESSION_FAILED_ATTEMPTS = 'auth.mfa.failed_attempts';
    private const SESSION_BLOCKED_UNTIL = 'auth.mfa.blocked_until';

    public function requiresMfa(User $user): bool
    {
        if (!config('mfa.enabled')) {
            return false;
        }

        if ((bool) config('mfa.allow_admin_bypass', false) && $user->isAdmin()) {
            return false;
        }

        $requiredUsers = $this->parseIntegerList((string) config('mfa.required_users', ''));
        if (in_array((int) $user->getAuthIdentifier(), $requiredUsers, true)) {
            return true;
        }

        $requiredGroups = $this->parseStringList((string) config('mfa.required_groups', ''));
        if (!empty($requiredGroups)) {
            $userGroups = $this->resolveGroupsForUser($user);
            foreach ($requiredGroups as $group) {
                if (in_array($group, $userGroups, true)) {
                    return true;
                }
            }

            return false;
        }

        if (!config('mfa.admins_only', true)) {
            return true;
        }

        return $user->isAdmin();
    }

    public function isVerified(User $user): bool
    {
        return (int) session(self::SESSION_VERIFIED_USER_ID) === (int) $user->getAuthIdentifier();
    }

    public function issueForUser(User $user, bool $force = false): void
    {
        if (!$force && $this->hasPendingForUser($user)) {
            return;
        }

        $code = $this->generateCode();
        session()->put(self::SESSION_USER_ID, (int) $user->getAuthIdentifier());
        session()->put(self::SESSION_CODE_HASH, Hash::make($code));
        session()->put(self::SESSION_EXPIRES_AT, Carbon::now()->addSeconds((int) config('mfa.ttl_seconds', 300))->timestamp);
        session()->forget(self::SESSION_VERIFIED_USER_ID);

        $this->notifyUser($user, $code);
    }

    public function verifyForUser(User $user, string $code): bool
    {
        if ($this->currentBlockSecondsForUser($user) > 0) {
            return false;
        }

        $sessionUser = (int) session(self::SESSION_USER_ID);
        $hash = (string) session(self::SESSION_CODE_HASH, '');
        $expiresAt = (int) session(self::SESSION_EXPIRES_AT, 0);

        if ($sessionUser !== (int) $user->getAuthIdentifier() || empty($hash) || time() > $expiresAt) {
            $this->registerVerifyFailure($user);
            return false;
        }

        if (!Hash::check(trim($code), $hash)) {
            $this->registerVerifyFailure($user);
            return false;
        }

        session()->put(self::SESSION_VERIFIED_USER_ID, (int) $user->getAuthIdentifier());
        session()->forget([self::SESSION_USER_ID, self::SESSION_CODE_HASH, self::SESSION_EXPIRES_AT]);
        $this->clearVerifyFailures($user);
        return true;
    }

    public function clear(User $user): void
    {
        if ((int) session(self::SESSION_VERIFIED_USER_ID) === (int) $user->getAuthIdentifier()) {
            session()->forget(self::SESSION_VERIFIED_USER_ID);
        }

        if ((int) session(self::SESSION_USER_ID) === (int) $user->getAuthIdentifier()) {
            session()->forget([self::SESSION_USER_ID, self::SESSION_CODE_HASH, self::SESSION_EXPIRES_AT]);
        }

        $this->clearVerifyFailures($user);
    }

    public function currentBlockSecondsForUser(User $user): int
    {
        $blocked = session(self::SESSION_BLOCKED_UNTIL, []);
        if (!is_array($blocked)) {
            return 0;
        }

        $userId = (int) $user->getAuthIdentifier();
        $blockedUser = (int) ($blocked['user_id'] ?? 0);
        $until = (int) ($blocked['until'] ?? 0);

        if ($blockedUser !== $userId || $until <= time()) {
            return 0;
        }

        return max(1, $until - time());
    }

    private function hasPendingForUser(User $user): bool
    {
        $sessionUser = (int) session(self::SESSION_USER_ID);
        $expiresAt = (int) session(self::SESSION_EXPIRES_AT, 0);
        return $sessionUser === (int) $user->getAuthIdentifier() && time() <= $expiresAt;
    }

    private function registerVerifyFailure(User $user): void
    {
        $userId = (int) $user->getAuthIdentifier();
        $failed = session(self::SESSION_FAILED_ATTEMPTS, []);
        if (!is_array($failed) || (int) ($failed['user_id'] ?? 0) !== $userId) {
            $failed = ['user_id' => $userId, 'count' => 0];
        }

        $failed['count'] = ((int) ($failed['count'] ?? 0)) + 1;
        session()->put(self::SESSION_FAILED_ATTEMPTS, $failed);

        $maxAttempts = max(1, (int) config('mfa.verify_max_attempts', 5));
        if ((int) $failed['count'] < $maxAttempts) {
            return;
        }

        $lockSeconds = $this->computeVerifyLockSeconds((int) $failed['count']);
        session()->put(self::SESSION_BLOCKED_UNTIL, [
            'user_id' => $userId,
            'until' => Carbon::now()->addSeconds($lockSeconds)->timestamp,
        ]);
    }

    private function clearVerifyFailures(User $user): void
    {
        $userId = (int) $user->getAuthIdentifier();

        $failed = session(self::SESSION_FAILED_ATTEMPTS, []);
        if (is_array($failed) && (int) ($failed['user_id'] ?? 0) === $userId) {
            session()->forget(self::SESSION_FAILED_ATTEMPTS);
        }

        $blocked = session(self::SESSION_BLOCKED_UNTIL, []);
        if (is_array($blocked) && (int) ($blocked['user_id'] ?? 0) === $userId) {
            session()->forget(self::SESSION_BLOCKED_UNTIL);
        }
    }

    private function computeVerifyLockSeconds(int $failures): int
    {
        if ($failures >= 12) {
            return max(60, (int) config('mfa.verify_lock_tertiary_seconds', 1800));
        }

        if ($failures >= 8) {
            return max(60, (int) config('mfa.verify_lock_secondary_seconds', 600));
        }

        return max(60, (int) config('mfa.verify_lock_primary_seconds', 120));
    }

    private function generateCode(): string
    {
        $length = max(4, min(8, (int) config('mfa.code_length', 6)));
        $max = (10 ** $length) - 1;
        return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
    }

    private function notifyUser(User $user, string $code): void
    {
        $identifier = $user->login ?? (string) $user->getAuthIdentifier();
        Log::info('MFA code issued', ['user' => $identifier, 'code' => $code]);

        $email = $user->email ?? $user->cgm?->z01_email ?? null;
        if (empty($email)) {
            return;
        }

        try {
            Mail::raw("Seu código MFA do e-Cidade é: {$code}", function ($message) use ($email) {
                $message->to($email)->subject('Código de verificação MFA');
            });
        } catch (\Throwable $e) {
            Log::warning('Failed to send MFA email', [
                'email' => $email,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return array<int, int>
     */
    private function parseIntegerList(string $value): array
    {
        $items = $this->parseStringList($value);
        $ids = [];

        foreach ($items as $item) {
            $id = (int) $item;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array<int, string>
     */
    private function parseStringList(string $value): array
    {
        $parts = array_map('trim', explode(',', $value));
        $parts = array_filter($parts, static function (string $part): bool {
            return $part !== '';
        });

        return array_values(array_unique($parts));
    }

    /**
     * @return array<int, string>
     */
    private function resolveGroupsForUser(User $user): array
    {
        $groups = ['default'];

        if ($user->isAdmin()) {
            $groups[] = 'admin';
        }

        if ((int) ($user->usuext ?? 0) === 1) {
            $groups[] = 'external';
        }

        $mapping = $this->decodeAssoc((string) config('mfa.user_groups_json', '{}'));
        $idKey = (string) $user->getAuthIdentifier();
        if (isset($mapping[$idKey]) && is_array($mapping[$idKey])) {
            foreach ($mapping[$idKey] as $group) {
                if (is_string($group) && trim($group) !== '') {
                    $groups[] = trim($group);
                }
            }
        }

        return array_values(array_unique($groups));
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeAssoc(string $json): array
    {
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
