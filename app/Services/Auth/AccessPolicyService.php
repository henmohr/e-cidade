<?php

namespace App\Services\Auth;

use App\Models\User;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class AccessPolicyService
{
    /**
     * @return array{allowed: bool, reason: string, detail: string}
     */
    public function evaluate(User $user, ?DateTimeInterface $now = null): array
    {
        if (!config('auth_access.enabled', false)) {
            return ['allowed' => true, 'reason' => AccessPolicyReasons::DISABLED, 'detail' => 'policy disabled'];
        }

        if ((bool) config('auth_access.allow_admin_bypass', false) && $user->isAdmin()) {
            return ['allowed' => true, 'reason' => AccessPolicyReasons::ADMIN_BYPASS, 'detail' => 'admin bypass enabled'];
        }

        $timezone = (string) config('auth_access.timezone', 'America/Sao_Paulo');
        $date = $this->resolveNow($timezone, $now);
        $rule = $this->resolveRuleForUser($user);

        $expiresAt = trim((string) ($rule['expires_at'] ?? ''));
        if ($expiresAt !== '') {
            $expires = $this->parseDate($expiresAt, $timezone);
            if ($expires !== null && $date > $expires) {
                return ['allowed' => false, 'reason' => AccessPolicyReasons::EXPIRED, 'detail' => 'user access expired'];
            }
        }

        $allowedWeekdays = $this->normalizeWeekdays($rule['allowed_weekdays'] ?? '');
        if (!empty($allowedWeekdays)) {
            $weekDay = (int) $date->format('N');
            if (!in_array($weekDay, $allowedWeekdays, true)) {
                return ['allowed' => false, 'reason' => AccessPolicyReasons::WEEKDAY, 'detail' => 'access not allowed for weekday'];
            }
        }

        $startTime = trim((string) ($rule['start_time'] ?? ''));
        $endTime = trim((string) ($rule['end_time'] ?? ''));
        if ($startTime !== '' && $endTime !== '') {
            $current = $date->format('H:i');
            $insideRange = $this->isInsideTimeWindow($current, $startTime, $endTime);
            if (!$insideRange) {
                return ['allowed' => false, 'reason' => AccessPolicyReasons::HOUR, 'detail' => 'outside allowed time window'];
            }
        }

        return ['allowed' => true, 'reason' => AccessPolicyReasons::ALLOWED, 'detail' => 'policy check passed'];
    }

    private function resolveNow(string $timezone, ?DateTimeInterface $now): DateTimeImmutable
    {
        if ($now === null) {
            return new DateTimeImmutable('now', new DateTimeZone($timezone));
        }

        return DateTimeImmutable::createFromInterface($now)
            ->setTimezone(new DateTimeZone($timezone));
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveRuleForUser(User $user): array
    {
        $rule = $this->normalizeRule((array) config('auth_access.default_rule', []));
        $groupRules = $this->decodeAssoc((string) config('auth_access.group_rules_json', '{}'));
        $userRules = $this->decodeAssoc((string) config('auth_access.user_rules_json', '{}'));

        foreach ($this->resolveGroupsForUser($user) as $group) {
            $groupRule = $groupRules[$group] ?? null;
            if (is_array($groupRule)) {
                $rule = array_merge($rule, $this->normalizeRule($groupRule));
            }
        }

        $idKey = (string) $user->getAuthIdentifier();
        if (isset($userRules[$idKey]) && is_array($userRules[$idKey])) {
            $rule = array_merge($rule, $this->normalizeRule($userRules[$idKey]));
        }

        return $rule;
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

        $mapping = $this->decodeAssoc((string) config('auth_access.user_groups_json', '{}'));
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
     * @param array<string, mixed> $rule
     * @return array<string, mixed>
     */
    private function normalizeRule(array $rule): array
    {
        return [
            'allowed_weekdays' => $rule['allowed_weekdays'] ?? '',
            'start_time' => $rule['start_time'] ?? '',
            'end_time' => $rule['end_time'] ?? '',
            'expires_at' => $rule['expires_at'] ?? '',
        ];
    }

    /**
     * @return array<int, int>
     */
    private function normalizeWeekdays($value): array
    {
        $values = [];

        if (is_array($value)) {
            $values = $value;
        } elseif (is_string($value)) {
            $value = trim($value);
            if ($value !== '') {
                $values = explode(',', $value);
            }
        }

        $weekdays = [];
        foreach ($values as $day) {
            $numeric = (int) trim((string) $day);
            if ($numeric === 0) {
                $numeric = 7;
            }
            if ($numeric >= 1 && $numeric <= 7) {
                $weekdays[] = $numeric;
            }
        }

        return array_values(array_unique($weekdays));
    }

    private function isInsideTimeWindow(string $current, string $start, string $end): bool
    {
        $current = substr($current, 0, 5);
        $start = substr($start, 0, 5);
        $end = substr($end, 0, 5);

        if ($start <= $end) {
            return $current >= $start && $current <= $end;
        }

        return $current >= $start || $current <= $end;
    }

    private function parseDate(string $value, string $timezone): ?DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value, new DateTimeZone($timezone));
        } catch (\Throwable $e) {
            return null;
        }
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
