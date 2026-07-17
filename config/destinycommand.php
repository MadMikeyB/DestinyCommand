<?php

return [
    'bungie_api_key' => env('BUNGIE_API_KEY'),

    'bungie_app_id' => env('BUNGIE_APP_ID'),

    'app_version' => env('APP_VERSION', 'local'),

    'user_agent' => env(
        'DESTINYCOMMAND_USER_AGENT',
        trim(
            'DestinyCommand/'.env('APP_VERSION', 'local')
            .' '.(env('BUNGIE_APP_ID') ? 'AppId/'.env('BUNGIE_APP_ID').' ' : '')
            .'(+'.rtrim((string) env('APP_URL', 'http://localhost'), '/').')'
        )
    ),

    'moderator_keys' => array_filter(
        explode(';', (string) env('MODERATOR_KEYS', ''))
    ),

    'request_origin' => env('DESTINYCOMMAND_REQUEST_ORIGIN'),
];
