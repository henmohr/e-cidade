<?php

return [
    'enabled' => env('AUTH_ACCESS_POLICY_ENABLED', false),
    'timezone' => env('AUTH_ACCESS_POLICY_TIMEZONE', 'America/Sao_Paulo'),
    'allow_admin_bypass' => env('AUTH_ACCESS_POLICY_ALLOW_ADMIN_BYPASS', false),
    'deny_message' => env('AUTH_ACCESS_POLICY_DENY_MESSAGE', 'Acesso bloqueado pela politica de horario/perfil.'),

    'default_rule' => [
        'allowed_weekdays' => env('AUTH_ACCESS_DEFAULT_ALLOWED_WEEKDAYS', ''),
        'start_time' => env('AUTH_ACCESS_DEFAULT_START_TIME', ''),
        'end_time' => env('AUTH_ACCESS_DEFAULT_END_TIME', ''),
        'expires_at' => env('AUTH_ACCESS_DEFAULT_EXPIRES_AT', ''),
    ],

    'group_rules_json' => env('AUTH_ACCESS_GROUP_RULES_JSON', '{}'),
    'user_rules_json' => env('AUTH_ACCESS_USER_RULES_JSON', '{}'),
    'user_groups_json' => env('AUTH_ACCESS_USER_GROUPS_JSON', '{}'),
];
