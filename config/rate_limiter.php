<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | This file contains the rate limiting configurations for your application.
    | These settings are used by the rate limiter to control the number of
    | requests a client can make within a given time frame.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Rate Limiter Configurations
    |--------------------------------------------------------------------------
    |
    | Here you can configure rate limiters for different parts of your
    | application. You can define multiple rate limiters, each with its own
    | unique configuration for maximum requests and time windows.
    |
    */

    'limiters' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'global' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
        ],
    ],
];
