<?php

return [
    'download_enabled' => env('BACKUP_DOWNLOAD_ENABLED', true),
    'directory' => env('BACKUP_DIR', '/var/backups/ecidade'),
    'a3_required' => env('BACKUP_A3_REQUIRED', true),
    'a3_allowed_issuer_regex' => env('BACKUP_A3_ALLOWED_ISSUER_REGEX', ''),
    'a3_allow_bypass' => env('BACKUP_A3_ALLOW_BYPASS', false),
    'download_link_ttl_minutes' => env('BACKUP_DOWNLOAD_LINK_TTL_MINUTES', 5),
];
