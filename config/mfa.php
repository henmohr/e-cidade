<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MFA Global Toggle
    |--------------------------------------------------------------------------
    */
    'enabled' => env('MFA_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Require MFA only for admin users
    |--------------------------------------------------------------------------
    */
    'admins_only' => env('MFA_ADMINS_ONLY', true),

    /*
    |--------------------------------------------------------------------------
    | Policy-driven targeting (incremental hardening)
    |--------------------------------------------------------------------------
    |
    | Supports requiring MFA by groups and explicit users while keeping
    | backwards compatibility with the existing admins_only toggle.
    |
    */
    'allow_admin_bypass' => env('MFA_ALLOW_ADMIN_BYPASS', false),
    'required_groups' => env('MFA_REQUIRED_GROUPS', ''),
    'required_users' => env('MFA_REQUIRED_USERS', ''),
    'user_groups_json' => env('MFA_USER_GROUPS_JSON', '{}'),

    /*
    |--------------------------------------------------------------------------
    | OTP code options
    |--------------------------------------------------------------------------
    */
    'code_length' => (int) env('MFA_CODE_LENGTH', 6),
    'ttl_seconds' => (int) env('MFA_TTL_SECONDS', 300),
];
