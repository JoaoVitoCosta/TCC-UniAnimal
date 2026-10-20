<?php

return [
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses' => ['key' => env('AWS_ACCESS_KEY_ID'), 'secret' => env('AWS_SECRET_ACCESS_KEY'), 'region' => env('AWS_DEFAULT_REGION', 'us-east-1')],
    'resend' => ['key' => env('RESEND_KEY')],
    'slack' => ['notifications' => ['bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'), 'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL')]],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    // Posição inicial de toda coleira recém-vinculada (SESI Tambaú),
    // até o dispositivo mandar o primeiro GPS.
    'coleira' => [
        'latitude_inicial'  => (float) env('COLEIRA_LATITUDE_INICIAL', -21.711289),
        'longitude_inicial' => (float) env('COLEIRA_LONGITUDE_INICIAL', -47.276060),
    ],
];
