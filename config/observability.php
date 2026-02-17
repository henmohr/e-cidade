<?php

return [
    'sla_target_percent' => (float) env('SLA_TARGET_PERCENT', 99.9),
    'sample_log_path' => env('SLA_SAMPLE_LOG_PATH', storage_path('logs/sla_samples.log')),
];
