<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FeatureFlag
{
    /**
     * Check if a feature is enabled for the current user/request
     *
     * @param string $featureName
     * @param mixed $user Optional user to check against
     * @return bool
     */
    public static function isEnabled(string $featureName, $user = null): bool
    {
        if (!Config::get('modern_routes.feature_flags.enabled', true)) {
            return false;
        }

        $feature = Config::get("modern_routes.feature_flags.features.{$featureName}");

        if (!$feature) {
            return Config::get('modern_routes.feature_flags.default_state') === 'enabled';
        }

        // Check if feature is globally enabled
        if (!($feature['enabled'] ?? false)) {
            return false;
        }

        // Check rollout percentage
        if (isset($feature['rollout_percentage'])) {
            $percentage = $feature['rollout_percentage'];
            
            if ($percentage === 0) {
                return false;
            }
            
            if ($percentage === 100) {
                return true;
            }

            // Use user ID or session ID for consistent rollout
            $identifier = $user ? $user->id : session()->getId();
            $hash = crc32($identifier . $featureName);
            $bucket = $hash % 100;
            
            return $bucket < $percentage;
        }

        // Check user-specific override (can be implemented later)
        if ($user && method_exists($user, 'hasFeatureFlag')) {
            $userOverride = $user->hasFeatureFlag($featureName);
            if ($userOverride !== null) {
                return $userOverride;
            }
        }

        return true;
    }

    /**
     * Enable a feature
     *
     * @param string $featureName
     * @return void
     */
    public static function enable(string $featureName): void
    {
        self::updateFeature($featureName, ['enabled' => true]);
    }

    /**
     * Disable a feature
     *
     * @param string $featureName
     * @return void
     */
    public static function disable(string $featureName): void
    {
        self::updateFeature($featureName, ['enabled' => false]);
    }

    /**
     * Set rollout percentage
     *
     * @param string $featureName
     * @param int $percentage 0-100
     * @return void
     */
    public static function setRolloutPercentage(string $featureName, int $percentage): void
    {
        $percentage = max(0, min(100, $percentage));
        self::updateFeature($featureName, [
            'enabled' => $percentage > 0,
            'rollout_percentage' => $percentage
        ]);
    }

    /**
     * Get all features and their status
     *
     * @return array
     */
    public static function all(): array
    {
        return Config::get('modern_routes.feature_flags.features', []);
    }

    /**
     * Update feature configuration
     *
     * @param string $featureName
     * @param array $updates
     * @return void
     */
    protected static function updateFeature(string $featureName, array $updates): void
    {
        $cacheKey = "feature_flags.{$featureName}";
        
        // Store in cache (in production, you'd store in database)
        Cache::forever($cacheKey, $updates);

        // Log the change
        if (Config::get('modern_routes.logging.enabled')) {
            Log::channel(Config::get('modern_routes.logging.channel'))
                ->info("Feature flag updated: {$featureName}", $updates);
        }
    }

    /**
     * Get feature configuration from cache or config
     *
     * @param string $featureName
     * @return array|null
     */
    protected static function getFeature(string $featureName): ?array
    {
        $cacheKey = "feature_flags.{$featureName}";
        
        // Check cache first
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return array_merge(
                Config::get("modern_routes.feature_flags.features.{$featureName}", []),
                $cached
            );
        }

        return Config::get("modern_routes.feature_flags.features.{$featureName}");
    }

    /**
     * Clear all feature flag cache
     *
     * @return void
     */
    public static function clearCache(): void
    {
        $features = Config::get('modern_routes.feature_flags.features', []);
        
        foreach (array_keys($features) as $featureName) {
            Cache::forget("feature_flags.{$featureName}");
        }
    }
}
