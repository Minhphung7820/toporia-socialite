<?php

declare(strict_types=1);

/**
 * Socialite Configuration
 *
 * Configuration for OAuth social authentication.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Google OAuth
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/socialite/google/callback'),
        'scopes' => [
            'openid',
            'profile',
            'email',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Facebook OAuth
    |--------------------------------------------------------------------------
    */
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', ''),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),
        'redirect' => env('FACEBOOK_REDIRECT_URI', '/auth/socialite/facebook/callback'),
        'scopes' => [
            'email',
            'public_profile',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub OAuth
    |--------------------------------------------------------------------------
    */
    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID', ''),
        'client_secret' => env('GITHUB_CLIENT_SECRET', ''),
        'redirect' => env('GITHUB_REDIRECT_URI', '/auth/socialite/github/callback'),
        'scopes' => [
            'user:email',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twitter/X OAuth 2.0
    |--------------------------------------------------------------------------
    */
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID', ''),
        'client_secret' => env('TWITTER_CLIENT_SECRET', ''),
        'redirect' => env('TWITTER_REDIRECT_URI', '/auth/socialite/twitter/callback'),
        'scopes' => [
            'tweet.read',
            'users.read',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LinkedIn OAuth
    |--------------------------------------------------------------------------
    */
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID', ''),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET', ''),
        'redirect' => env('LINKEDIN_REDIRECT_URI', '/auth/socialite/linkedin/callback'),
        'scopes' => [
            'openid',
            'profile',
            'email',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    // Allowed domains for redirect after OAuth callback (open redirect protection)
    // Only URLs from these domains will be allowed, in addition to relative URLs
    'allowed_redirect_domains' => array_filter(explode(',', env('SOCIALITE_ALLOWED_REDIRECT_DOMAINS', ''))),

    // Token encryption - encrypt tokens before storing in database
    'encrypt_tokens' => env('SOCIALITE_ENCRYPT_TOKENS', true),

    // Token refresh - automatically refresh expired tokens
    'auto_refresh_tokens' => env('SOCIALITE_AUTO_REFRESH_TOKENS', true),

    // Stateless mode - don't use session storage for state parameter
    'stateless' => env('SOCIALITE_STATELESS', false),
];
