<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Modern Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Define which routes are handled by the modern application vs legacy code.
    | Routes are evaluated in order - first match wins.
    |
    */

    'enabled' => env('MODERN_ROUTES_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Modern Route Patterns
    |--------------------------------------------------------------------------
    |
    | List of route patterns that should be handled by the modern application.
    | Uses fnmatch patterns (* and ? wildcards).
    |
    */
    'modern_patterns' => [
        // API Routes (modern REST API)
        'api/v2/*',
        
        // Nova interface
        'nova-interface/*',
        'dashboard/novo/*',
        
        // Módulos modernizados (exemplos - ativar conforme for migrando)
        // 'empenho/novo/*',
        // 'contabilidade/novo/*',
        // 'tributario/novo/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Route Patterns
    |--------------------------------------------------------------------------
    |
    | Explicit legacy patterns (optional - by default everything not modern is legacy)
    | Useful for documentation and explicit control.
    |
    */
    'legacy_patterns' => [
        'login.php',
        'abrir.php',
        '*.php',  // All .php files go to legacy by default
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable specific modern routes with feature flags.
    | Can be controlled per user or globally.
    |
    */
    'feature_flags' => [
        'enabled' => env('FEATURE_FLAGS_ENABLED', true),
        
        // Default state for new features
        'default_state' => 'disabled',
        
        // Features configuration
        'features' => [
            'api_v2' => [
                'enabled' => true,
                'description' => 'Nova API REST v2',
                'routes' => ['api/v2/*'],
            ],
            'nova_interface' => [
                'enabled' => false,
                'description' => 'Nova interface do usuário',
                'routes' => ['nova-interface/*'],
                'rollout_percentage' => 0, // 0-100
            ],
            'dashboard_novo' => [
                'enabled' => false,
                'description' => 'Novo dashboard',
                'routes' => ['dashboard/novo/*'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Management
    |--------------------------------------------------------------------------
    |
    | Share session between modern and legacy code
    |
    */
    'session' => [
        'share_with_legacy' => true,
        'legacy_session_name' => 'PHPSESSID',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Log route decisions for debugging and monitoring
    |
    */
    'logging' => [
        'enabled' => env('LOG_ROUTE_DECISIONS', false),
        'channel' => 'stack',
    ],

];
