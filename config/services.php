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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
    'secret' => env('SES_KEY_SECRET'),
    'region' => env('SES_REGION'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_APP_ID'),
        'client_secret' => env('GOOGLE_APP_SECRET'),
        'redirect' => env('GOOGLE_APP_RETURN_URL'),
    ],
     'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'plan' => [
            'webmaster_id' => env('WEBMASTER_ID'),
            'webmaster_price' => env('WEBMASTER_PRICE'),
            'business_id' => env('BUSINESS_ID'),
            'business_price' => env('BUSINESS_PRICE'),
            'agency_id' => env('AGENCY_ID'),
            'agency_price' => env('AGENCY_PRICE'),
        ],
    ],

];

