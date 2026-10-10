<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | One guard, one provider, one person. There is no users table.
    |
    */

    'defaults' => [
        'guard' => 'web',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'owner',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | The "owner" driver is registered in the AppServiceProvider and hands the
    | session guard an Owner without touching the database.
    |
    */

    'providers' => [
        'owner' => [
            'driver' => 'owner',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Owner Email
    |--------------------------------------------------------------------------
    |
    | The only Google account allowed to sign in. Compared exactly, never by
    | domain.
    |
    */

    'owner_email' => env('OWNER_EMAIL'),

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
