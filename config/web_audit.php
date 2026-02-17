<?php

return [
    'enabled' => env('WEB_AUDIT_ENABLED', true),
    'channel' => env('WEB_AUDIT_CHANNEL', 'web_audit'),
    'include_query' => env('WEB_AUDIT_INCLUDE_QUERY', true),
    'include_input_keys' => env('WEB_AUDIT_INCLUDE_INPUT_KEYS', true),
    'exclude_paths' => [
        'web/mfa/challenge',
        'web/mfa/resend',
    ],
    'sensitive_keys' => [
        'senha',
        'password',
        'token',
        'code',
        'signature',
    ],
];
