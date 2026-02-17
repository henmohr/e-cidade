<?php

return [
    'enabled' => env('AUTH_EXTERNAL_ENABLED', false),
    'allowed_providers' => env('AUTH_EXTERNAL_ALLOWED_PROVIDERS', 'govbr,google,a1,a3'),
    'provider_secrets_json' => env('AUTH_EXTERNAL_PROVIDER_SECRETS_JSON', '{}'),
    'allow_unsigned' => env('AUTH_EXTERNAL_ALLOW_UNSIGNED', false),
    'default_instit' => (int) env('AUTH_EXTERNAL_DEFAULT_INSTIT', 1),
    'redirect_path' => env('AUTH_EXTERNAL_REDIRECT_PATH', '/web/welcome'),
];
