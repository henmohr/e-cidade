<?php

namespace App\Console\Commands;

use App\Services\FeatureFlag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class FeatureFlagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature:flag 
                            {action : Action to perform (list|enable|disable|rollout|status)}
                            {feature? : Feature name}
                            {value? : Value for the action (percentage for rollout)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage feature flags for modern/legacy routing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $feature = $this->argument('feature');
        $value = $this->argument('value');

        switch ($action) {
            case 'list':
                return $this->listFeatures();
            case 'enable':
                return $this->enableFeature($feature);
            case 'disable':
                return $this->disableFeature($feature);
            case 'rollout':
                return $this->rolloutFeature($feature, $value);
            case 'status':
                return $this->featureStatus($feature);
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    /**
     * List all features
     *
     * @return int
     */
    protected function listFeatures(): int
    {
        $features = FeatureFlag::all();

        if (empty($features)) {
            $this->info('No features configured.');
            return 0;
        }

        $this->info('Feature Flags:');
        $this->newLine();

        $rows = [];
        foreach ($features as $name => $config) {
            $rows[] = [
                $name,
                $config['enabled'] ? '✓ Enabled' : '✗ Disabled',
                $config['rollout_percentage'] ?? 'N/A',
                $config['description'] ?? '',
            ];
        }

        $this->table(
            ['Feature', 'Status', 'Rollout %', 'Description'],
            $rows
        );

        return 0;
    }

    /**
     * Enable a feature
     *
     * @param string|null $feature
     * @return int
     */
    protected function enableFeature(?string $feature): int
    {
        if (!$feature) {
            $this->error('Feature name is required');
            return 1;
        }

        FeatureFlag::enable($feature);
        $this->info("Feature '{$feature}' enabled successfully!");
        
        return 0;
    }

    /**
     * Disable a feature
     *
     * @param string|null $feature
     * @return int
     */
    protected function disableFeature(?string $feature): int
    {
        if (!$feature) {
            $this->error('Feature name is required');
            return 1;
        }

        FeatureFlag::disable($feature);
        $this->info("Feature '{$feature}' disabled successfully!");
        
        return 0;
    }

    /**
     * Set rollout percentage for a feature
     *
     * @param string|null $feature
     * @param string|null $percentage
     * @return int
     */
    protected function rolloutFeature(?string $feature, ?string $percentage): int
    {
        if (!$feature) {
            $this->error('Feature name is required');
            return 1;
        }

        if ($percentage === null) {
            $this->error('Percentage value is required');
            return 1;
        }

        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 100) {
            $this->error('Percentage must be between 0 and 100');
            return 1;
        }

        FeatureFlag::setRolloutPercentage($feature, (int)$percentage);
        $this->info("Feature '{$feature}' rollout set to {$percentage}%");
        
        return 0;
    }

    /**
     * Show status of a specific feature
     *
     * @param string|null $feature
     * @return int
     */
    protected function featureStatus(?string $feature): int
    {
        if (!$feature) {
            $this->error('Feature name is required');
            return 1;
        }

        $config = Config::get("modern_routes.feature_flags.features.{$feature}");

        if (!$config) {
            $this->error("Feature '{$feature}' not found");
            return 1;
        }

        $this->info("Feature: {$feature}");
        $this->newLine();
        $this->line("Status: " . ($config['enabled'] ? '✓ Enabled' : '✗ Disabled'));
        $this->line("Description: " . ($config['description'] ?? 'N/A'));
        
        if (isset($config['rollout_percentage'])) {
            $this->line("Rollout: {$config['rollout_percentage']}%");
        }

        if (isset($config['routes'])) {
            $this->newLine();
            $this->line("Routes:");
            foreach ($config['routes'] as $route) {
                $this->line("  - {$route}");
            }
        }

        return 0;
    }
}
