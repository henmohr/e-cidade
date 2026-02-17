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
        $sessionUser = (int) session(self::SESSION_USER_ID);
        $hash = (string) session(self::SESSION_CODE_HASH, '');
        $expiresAt = (int) session(self::SESSION_EXPIRES_AT, 0);

        if ($sessionUser !== (int) $user->getAuthIdentifier() || empty($hash) || time() > $expiresAt) {
            return false;
        }

        if (!Hash::check(trim($code), $hash)) {
            return false;
        }

        session()->put(self::SESSION_VERIFIED_USER_ID, (int) $user->getAuthIdentifier());
        session()->forget([self::SESSION_USER_ID, self::SESSION_CODE_HASH, self::SESSION_EXPIRES_AT]);
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
    }

    private function hasPendingForUser(User $user): bool
    {
        $sessionUser = (int) session(self::SESSION_USER_ID);
        $expiresAt = (int) session(self::SESSION_EXPIRES_AT, 0);
        return $sessionUser === (int) $user->getAuthIdentifier() && time() <= $expiresAt;
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
