<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'qdrant' => [
        'url' => env('QDRANT_URL'),
        'api_key' => env('QDRANT_APIKEY'),
    ],

    'embedding' => [
        'url' => env('EMBEDDING_URL', 'http://localhost:8989'),
        'dimensions' => env('EMBEDDING_DIMENSIONS', 384),
        'timeout' => env('EMBEDDING_TIMEOUT', 30),
        'batch_size' => env('EMBEDDING_BATCH_SIZE', 100),
    ],

    'awin' => [
        'url' => env('AWIN_API_URL', 'https://aw-data.monitordeprecos.com.br'),
        'token' => env('AWIN_API_TOKEN', ''),
    ],

    'markdown_feed' => [
        'token' => env('MARKDOWN_FEED_TOKEN'),
    ],

];
