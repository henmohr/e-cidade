<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (!config('observability.schedule_enabled', false)) {
            return;
        }

        $interval = max(1, (int) config('observability.health_snapshot_interval_minutes', 5));
        $baseUrl = (string) config('observability.health_snapshot_base_url', config('app.url'));
        $slaHours = max(1, (int) config('observability.sla_report_hours', 24));

        $schedule->command('ops:health-snapshot', [
            '--base-url' => $baseUrl,
            '--append-log' => true,
        ])->cron("*/{$interval} * * * *")->withoutOverlapping();

        $schedule->command('ops:sla-report', [
            '--hours' => $slaHours,
            '--format' => 'json',
            '--append-log' => true,
        ])->hourly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
