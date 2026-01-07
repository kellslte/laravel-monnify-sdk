<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Monnify API Credentials
    |--------------------------------------------------------------------------
    |
    | These are your Monnify API credentials. You can get them from your
    | Monnify dashboard. The API key and secret key are used for authentication.
    |
    */

    'api_key' => env('MONNIFY_API_KEY', ''),

    'secret_key' => env('MONNIFY_SECRET_KEY', ''),

    'contract_code' => env('MONNIFY_CONTRACT_CODE', ''),

    /*
    |--------------------------------------------------------------------------
    | Monnify Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Monnify API. Use the sandbox URL for testing
    | and the production URL for live transactions.
    |
    */

    'base_url' => env('MONNIFY_BASE_URL', 'https://api.monnify.com'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to verify webhook signatures from Monnify.
    | This should match the webhook secret configured in your Monnify dashboard.
    |
    */

    'webhook_secret' => env('MONNIFY_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching access tokens. Tokens are cached to reduce
    | API calls and improve performance.
    |
    */

    'cache' => [
        'token_key' => 'monnify_access_token',
        'ttl' => 3600, // Token TTL in seconds (1 hour)
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | These are the table names for storing Monnify transactions and invoices
    | locally. You can customize these if needed.
    |
    */

    'tables' => [
        'transactions' => 'monnify_transactions',
        'invoices' => 'monnify_invoices',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for webhook routes.
    |
    */

    'routes' => [
        'prefix' => 'api/monnify',
        'middleware' => ['api'],
        'webhook' => 'webhook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Transaction Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for transactions when not explicitly provided.
    |
    */

    'transaction' => [
        'currency' => 'NGN',
        'redirect_url' => env('MONNIFY_REDIRECT_URL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for invoices when not explicitly provided.
    |
    */

    'invoice' => [
        'currency' => 'NGN',
        'redirect_url' => env('MONNIFY_REDIRECT_URL', ''),
    ],

];
