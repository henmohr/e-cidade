<?php

return [
    'enabled' => env('AUTH_EXTERNAL_ENABLED', false),
    'allowed_providers' => env('AUTH_EXTERNAL_ALLOWED_PROVIDERS', 'govbr,google,a1,a3'),
    'provider_secrets_json' => env('AUTH_EXTERNAL_PROVIDER_SECRETS_JSON', '{}'),
    'allow_unsigned' => env('AUTH_EXTERNAL_ALLOW_UNSIGNED', false),
    'default_instit' => (int) env('AUTH_EXTERNAL_DEFAULT_INSTIT', 1),
    'redirect_path' => env('AUTH_EXTERNAL_REDIRECT_PATH', '/web/welcome'),
    'enforce_claims_expiration' => env('AUTH_EXTERNAL_ENFORCE_CLAIMS_EXPIRATION', true),
    'max_clock_skew_seconds' => (int) env('AUTH_EXTERNAL_MAX_CLOCK_SKEW_SECONDS', 60),
    'enforce_nonce' => env('AUTH_EXTERNAL_ENFORCE_NONCE', true),
    'nonce_ttl_seconds' => (int) env('AUTH_EXTERNAL_NONCE_TTL_SECONDS', 600),
];
