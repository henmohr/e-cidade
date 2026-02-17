<?php

return [
    'sla_target_percent' => (float) env('SLA_TARGET_PERCENT', 99.9),
    'sample_log_path' => env('SLA_SAMPLE_LOG_PATH', storage_path('logs/sla_samples.log')),
    'sla_report_log_path' => env('SLA_REPORT_LOG_PATH', storage_path('logs/sla_reports.log')),
    'schedule_enabled' => env('OPS_SCHEDULE_ENABLED', false),
    'health_snapshot_interval_minutes' => (int) env('OPS_HEALTH_SNAPSHOT_INTERVAL_MINUTES', 5),
    'health_snapshot_base_url' => env('OPS_HEALTH_SNAPSHOT_BASE_URL', env('APP_URL', 'http://localhost')),
    'sla_report_hours' => (int) env('OPS_SLA_REPORT_HOURS', 24),
];
