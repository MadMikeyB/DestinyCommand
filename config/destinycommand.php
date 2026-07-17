<?php

return [
    'bungie_api_key' => env('BUNGIE_API_KEY'),

    'user_agent' => env(
        'DESTINYCOMMAND_USER_AGENT',
        'DestinyCommand/laravel13 (+'.rtrim((string) env('APP_URL', 'http://localhost'), '/').')'
    ),

    'moderator_keys' => array_filter(
        explode(';', (string) env('MODERATOR_KEYS', ''))
    ),

    'request_origin' => env(
        'DESTINYCOMMAND_REQUEST_ORIGIN',
        rtrim((string) env('APP_URL', 'http://localhost'), '/')
    ),
];
