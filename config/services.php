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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'web3auth' => [
        'client_id' => 'BFcEYcKaDaVLDOQXYPk1rpJHxJkxZa0oZsCf22YIoARnC-85o8hMZE3Kboy5V8vkcyMOws3STJQm5HfG01Da20Q',
        'network' => 'sapphire_devnet',
        
        // 'ui' => [
        //     'app_name' => 'JagaBumi',
        //     'mode' => 'dark',
        //     'login_methods_order' => ['google'],
        //     'default_language' => 'en',
        //     'logo_light' => 'https://web3auth.io/images/w3a-L-Favicon-1.svg',
        // ],
    ],

    'zksync' => [
        'chain_namespace' => 'eip155',
        'chain_id' => '0x12c',
        'rpc_target' => 'https://sepolia.era.zksync.dev',
        'display_name' => 'ZKsync Sepolia Testnet',
        'block_explorer' => 'https://sepolia.explorer.zksync.io/',
        'ticker' => 'ETH',
        'ticker_name' => 'Ethereum',
    ],

];
