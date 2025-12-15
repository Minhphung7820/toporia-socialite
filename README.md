# Toporia Socialite

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Framework](https://img.shields.io/badge/framework-Toporia-orange.svg)](https://github.com/Minhphung7820/toporia)

OAuth social authentication package for Toporia Framework with support for Google, Facebook, GitHub, Twitter, and LinkedIn.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Supported Providers](#supported-providers)
- [Basic Usage](#basic-usage)
- [Advanced Features](#advanced-features)
- [Security](#security)
- [Database Schema](#database-schema)
- [Code Examples](#code-examples)
- [Troubleshooting](#troubleshooting)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

### OAuth 2.0 Support
- **Multiple Providers**: Google, Facebook, GitHub, Twitter/X, LinkedIn
- **Standard OAuth Flow**: Full OAuth 2.0 authorization code flow
- **Automatic User Mapping**: Maps provider data to User objects
- **Custom Providers**: Easy extension for additional OAuth providers

### Security
- **CSRF Protection**: State parameter validation prevents CSRF attacks
- **Token Encryption**: Tokens encrypted at rest in database
- **Open Redirect Protection**: Validates redirect URLs
- **Timing-Safe Comparisons**: Uses `hash_equals()` for state validation
- **Token Refresh**: Automatic token refresh for expired access tokens

### Developer Experience
- **Simple API**: Clean, intuitive interface
- **Helper Functions**: Global `socialite()` helper
- **Account Linking**: Link multiple social accounts to one user
- **Stateless Mode**: Optional stateless authentication
- **Token Management**: Store and refresh OAuth tokens

## Requirements

- **PHP**: >= 8.1
- **Toporia Framework**: ^1.0
- **Extensions**:
  - `ext-json` (required)
  - `ext-openssl` (required, for token encryption)

## Installation

### 1. Install via Composer

```bash
composer require toporia/socialite
```

### 2. Publish Configuration

```bash
php console vendor:publish --provider="Toporia\Socialite\SocialiteServiceProvider"
```

This publishes `config/socialite.php` to your application.

### 3. Run Migrations

```bash
php console migrate
```

This creates the `social_accounts` table for linking OAuth accounts to users.

### 4. Configure OAuth Providers

Add to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/socialite/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/socialite/facebook/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
GITHUB_REDIRECT_URI=http://localhost:8000/auth/socialite/github/callback

# Twitter OAuth 2.0
TWITTER_CLIENT_ID=your-twitter-client-id
TWITTER_CLIENT_SECRET=your-twitter-client-secret
TWITTER_REDIRECT_URI=http://localhost:8000/auth/socialite/twitter/callback

# LinkedIn OAuth
LINKEDIN_CLIENT_ID=your-linkedin-client-id
LINKEDIN_CLIENT_SECRET=your-linkedin-client-secret
LINKEDIN_REDIRECT_URI=http://localhost:8000/auth/socialite/linkedin/callback

# Security Settings
SOCIALITE_ENCRYPT_TOKENS=true
SOCIALITE_AUTO_REFRESH_TOKENS=true
SOCIALITE_STATELESS=false
```

## Quick Start

### Redirect to Provider

```php
use Toporia\Socialite\SocialiteManager;

$manager = app(SocialiteManager::class);

// Redirect to Google OAuth
$url = $manager->driver('google')->redirect();
return redirect($url);
```

### Handle Callback

```php
use Toporia\Socialite\SocialiteManager;

$manager = app(SocialiteManager::class);

// Get user from Google
$user = $manager->driver('google')->user($request);

// User data available:
echo $user->id;        // Provider user ID
echo $user->name;      // Full name
echo $user->email;     // Email address
echo $user->avatar;    // Profile picture URL
echo $user->nickname;  // Username/nickname
```

## Configuration

### Configuration File (`config/socialite.php`)

```php
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
    'allowed_redirect_domains' => array_filter(explode(',', env('SOCIALITE_ALLOWED_REDIRECT_DOMAINS', ''))),

    // Token encryption - encrypt tokens before storing in database
    'encrypt_tokens' => env('SOCIALITE_ENCRYPT_TOKENS', true),

    // Token refresh - automatically refresh expired tokens
    'auto_refresh_tokens' => env('SOCIALITE_AUTO_REFRESH_TOKENS', true),

    // Stateless mode - don't use session storage for state parameter
    'stateless' => env('SOCIALITE_STATELESS', false),
];
```

## Supported Providers

### Google

```php
$user = socialite('google')->user($request);

// Available data:
// - id: Google user ID
// - name: Full name
// - email: Email address
// - avatar: Profile picture URL
// - nickname: null (Google doesn't provide)
```

**Required Scopes**: `openid`, `profile`, `email`

**OAuth Console**: [Google Cloud Console](https://console.cloud.google.com/apis/credentials)

### Facebook

```php
$user = socialite('facebook')->user($request);

// Available data:
// - id: Facebook user ID
// - name: Full name
// - email: Email address (requires email scope)
// - avatar: Profile picture URL
// - nickname: null
```

**Required Scopes**: `email`, `public_profile`

**OAuth Console**: [Facebook Developers](https://developers.facebook.com/apps/)

### GitHub

```php
$user = socialite('github')->user($request);

// Available data:
// - id: GitHub user ID
// - name: Full name
// - email: Email address
// - avatar: Avatar URL
// - nickname: GitHub username
```

**Required Scopes**: `user:email`

**OAuth Console**: [GitHub Developer Settings](https://github.com/settings/developers)

### Twitter/X

```php
$user = socialite('twitter')->user($request);

// Available data:
// - id: Twitter user ID
// - name: Display name
// - email: Email address (requires email scope)
// - avatar: Profile picture URL
// - nickname: Twitter handle
```

**Required Scopes**: `tweet.read`, `users.read`

**OAuth Console**: [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard)

### LinkedIn

```php
$user = socialite('linkedin')->user($request);

// Available data:
// - id: LinkedIn user ID
// - name: Full name
// - email: Email address
// - avatar: Profile picture URL
// - nickname: null
```

**Required Scopes**: `openid`, `profile`, `email`

**OAuth Console**: [LinkedIn Developers](https://www.linkedin.com/developers/apps)

## Basic Usage

### Complete Authentication Flow

```php
namespace App\Presentation\Http\Controllers;

use Toporia\Framework\Http\{Request, RedirectResponse};
use Toporia\Socialite\SocialiteManager;
use Toporia\Socialite\Models\SocialAccount;
use App\Domain\Models\UserModel;

class SocialAuthController
{
    public function __construct(
        private SocialiteManager $socialite
    ) {}

    /**
     * Redirect to OAuth provider
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        $url = $this->socialite->driver($provider)->redirect();
        return redirect($url);
    }

    /**
     * Handle OAuth callback
     */
    public function handleProviderCallback(Request $request, string $provider): RedirectResponse
    {
        try {
            // Get user data from provider
            $socialUser = $this->socialite->driver($provider)->user($request);

            // Find or create user
            $user = $this->findOrCreateUser($socialUser, $provider);

            // Link social account
            $this->linkSocialAccount($user, $socialUser, $provider);

            // Login user
            auth()->login($user);

            return redirect('/dashboard');
        } catch (\Throwable $e) {
            logger()->error('OAuth authentication failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect('/login')->with('error', 'Authentication failed. Please try again.');
        }
    }

    /**
     * Find or create user from social data
     */
    private function findOrCreateUser($socialUser, string $provider): UserModel
    {
        // Check if social account already exists
        $socialAccount = SocialAccount::findByProvider($provider, $socialUser->id);

        if ($socialAccount && $socialAccount->user) {
            return $socialAccount->user;
        }

        // Check if user exists by email
        $user = UserModel::where('email', $socialUser->email)->first();

        if (!$user) {
            // Create new user
            $user = UserModel::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'avatar' => $socialUser->avatar,
                'email_verified_at' => now(), // OAuth emails are pre-verified
            ]);
        }

        return $user;
    }

    /**
     * Link social account to user
     */
    private function linkSocialAccount(UserModel $user, $socialUser, string $provider): void
    {
        SocialAccount::updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $socialUser->id,
            ],
            [
                'user_id' => $user->id,
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'avatar' => $socialUser->avatar,
                'nickname' => $socialUser->nickname,
                'metadata' => $socialUser->attributes,
            ]
        );
    }
}
```

### Routes

```php
// routes/web.php

use App\Presentation\Http\Controllers\SocialAuthController;

$router->get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider']);
$router->get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
```

### Using Helper Function

```php
// Redirect to provider
return redirect(socialite('google')->redirect());

// Get user from callback
$user = socialite('google')->user($request);
```

## Advanced Features

### Token Refresh

Automatically refresh expired access tokens:

```php
use Toporia\Socialite\Models\SocialAccount;
use Toporia\Socialite\SocialiteManager;

$account = SocialAccount::where('user_id', $userId)
    ->where('provider', 'google')
    ->first();

// Check if token is expired
if ($account->isTokenExpired() && $account->hasRefreshToken()) {
    $manager = app(SocialiteManager::class);
    $provider = $manager->driver('google');

    // Refresh the token
    $tokens = $provider->refreshToken($account->provider_refresh_token);

    // Update stored tokens
    $account->update([
        'provider_token' => $tokens['access_token'],
        'provider_refresh_token' => $tokens['refresh_token'] ?? $account->provider_refresh_token,
        'provider_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
    ]);
}
```

### Stateless Mode

Use stateless mode for API or server-to-server flows (skips CSRF state validation):

```php
// WARNING: Only use for trusted server-to-server flows
// NOT recommended for browser-based authentication

$user = socialite('google')
    ->stateless()
    ->user($request);
```

### Custom Scopes

Override default scopes per provider:

```php
// In config/socialite.php
'google' => [
    'scopes' => [
        'openid',
        'profile',
        'email',
        'https://www.googleapis.com/auth/calendar', // Custom scope
        'https://www.googleapis.com/auth/drive.readonly',
    ],
],
```

### Token Storage

Store OAuth tokens for API access:

```php
use Toporia\Socialite\Models\SocialAccount;

// After OAuth callback
$socialUser = socialite('google')->user($request);
$token = $request->input('access_token'); // Get from provider response

SocialAccount::create([
    'user_id' => $user->id,
    'provider' => 'google',
    'provider_id' => $socialUser->id,
    'provider_token' => $token, // Automatically encrypted
    'provider_refresh_token' => $request->input('refresh_token'),
    'provider_expires_at' => now()->addHours(1),
    'name' => $socialUser->name,
    'email' => $socialUser->email,
    'avatar' => $socialUser->avatar,
]);
```

### Custom Providers

Extend support for additional OAuth providers:

```php
namespace App\Infrastructure\Socialite\Providers;

use Toporia\Socialite\AbstractProvider;
use Toporia\Socialite\User;

class DiscordProvider extends AbstractProvider
{
    protected function getAuthUrl(): string
    {
        return 'https://discord.com/api/oauth2/authorize';
    }

    protected function getTokenUrl(): string
    {
        return 'https://discord.com/api/oauth2/token';
    }

    protected function getUserUrl(): string
    {
        return 'https://discord.com/api/users/@me';
    }

    protected function mapUserToObject(array $user): User
    {
        return new User(
            id: (string) $user['id'],
            name: $user['username'],
            email: $user['email'] ?? '',
            avatar: $user['avatar']
                ? "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.png"
                : null,
            nickname: $user['username'],
            attributes: $user
        );
    }

    protected function getUserFromToken(string $token): User
    {
        $userData = $this->getUserData($token);
        return $this->mapUserToObject($userData);
    }
}
```

Register custom provider:

```php
// In AppServiceProvider or bootstrap
use Toporia\Socialite\SocialiteManager;

$manager = app(SocialiteManager::class);

$manager->extend('discord', function ($httpClient, $config) {
    return new DiscordProvider(
        $config['client_id'],
        $config['client_secret'],
        $config['redirect'],
        $httpClient,
        $config['scopes'] ?? []
    );
});
```

## Security

### CSRF Protection (State Parameter)

All OAuth flows include CSRF protection via state parameter:

```php
// Automatically generated and verified
// State is stored in session and validated on callback
// Uses timing-safe comparison (hash_equals)

// Flow:
// 1. Generate random state: Str::random(40)
// 2. Store in session: $_SESSION['socialite_state']
// 3. Include in redirect URL: ?state=abc123...
// 4. Verify on callback: hash_equals($stored, $received)
```

### Token Encryption

OAuth tokens are encrypted at rest:

```php
// In SocialAccount model
protected static array $casts = [
    'provider_token' => 'encrypted',
    'provider_refresh_token' => 'encrypted',
];

// Tokens automatically encrypted when stored
// Tokens automatically decrypted when retrieved
```

### Open Redirect Protection

Validate redirect URLs to prevent open redirect attacks:

```php
// In config/socialite.php
'allowed_redirect_domains' => [
    'yourdomain.com',
    'staging.yourdomain.com',
],

// Only URLs from these domains (or relative URLs) are allowed
```

### Security Best Practices

1. **Always Use HTTPS**: Never use OAuth over HTTP in production
2. **Validate State**: Always verify state parameter (enabled by default)
3. **Encrypt Tokens**: Store tokens encrypted (enabled by default)
4. **Rotate Secrets**: Rotate OAuth client secrets regularly
5. **Minimal Scopes**: Request only necessary scopes
6. **Token Expiry**: Always check token expiration before use
7. **Secure Storage**: Store client secrets in environment variables, never in code

## Database Schema

### `social_accounts` Table

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| user_id | BIGINT | Foreign key to users table |
| provider | VARCHAR(50) | OAuth provider (google, facebook, etc.) |
| provider_id | VARCHAR(255) | Provider-specific user ID |
| provider_token | TEXT | Access token (encrypted) |
| provider_refresh_token | TEXT | Refresh token (encrypted) |
| provider_expires_at | TIMESTAMP | Token expiration time |
| name | VARCHAR(255) | User's name from provider |
| email | VARCHAR(255) | User's email from provider |
| avatar | TEXT | Profile picture URL |
| nickname | VARCHAR(255) | Username/nickname |
| metadata | JSON | Additional provider data |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

**Indexes:**
- Composite unique index on `(provider, provider_id)`
- Index on `user_id`
- Index on `email`

## Code Examples

### Link Multiple Social Accounts

```php
use Toporia\Socialite\Models\SocialAccount;

// User can link multiple providers to one account
$user = auth()->user();

// Link Google
SocialAccount::create([
    'user_id' => $user->id,
    'provider' => 'google',
    'provider_id' => $googleUser->id,
    'name' => $googleUser->name,
    'email' => $googleUser->email,
]);

// Link Facebook
SocialAccount::create([
    'user_id' => $user->id,
    'provider' => 'facebook',
    'provider_id' => $facebookUser->id,
    'name' => $facebookUser->name,
    'email' => $facebookUser->email,
]);

// Get all linked accounts
$accounts = SocialAccount::where('user_id', $user->id)->get();
```

### Account Settings Page

```php
class AccountSettingsController
{
    public function showLinkedAccounts()
    {
        $user = auth()->user();
        $linkedAccounts = SocialAccount::where('user_id', $user->id)->get();

        return view('settings.accounts', [
            'linkedAccounts' => $linkedAccounts,
            'availableProviders' => ['google', 'facebook', 'github', 'twitter', 'linkedin'],
        ]);
    }

    public function unlinkAccount(string $provider)
    {
        $user = auth()->user();

        SocialAccount::where('user_id', $user->id)
            ->where('provider', $provider)
            ->delete();

        return redirect('/settings/accounts')->with('success', 'Account unlinked successfully');
    }
}
```

### API Authentication with Social Token

```php
use Toporia\Socialite\Models\SocialAccount;

class ApiController
{
    public function getUserCalendar()
    {
        $user = auth()->user();
        $account = SocialAccount::where('user_id', $user->id)
            ->where('provider', 'google')
            ->first();

        if (!$account) {
            return response()->json(['error' => 'Google account not linked'], 400);
        }

        // Check if token is expired
        if ($account->isTokenExpired() && $account->hasRefreshToken()) {
            // Refresh token logic here
        }

        // Use token to call Google Calendar API
        $client = app(HttpClientInterface::class);
        $response = $client
            ->withToken($account->provider_token)
            ->get('https://www.googleapis.com/calendar/v3/calendars/primary/events');

        return response()->json($response->json());
    }
}
```

## Troubleshooting

### Invalid State Exception

**Error**: `Invalid or missing state parameter`

**Causes:**
1. Session not persisting between redirect and callback
2. Multiple OAuth attempts in different tabs
3. Session expired during OAuth flow
4. State parameter modified or missing

**Solutions:**
```php
// Check session configuration
// config/session.php
'driver' => 'file', // or 'database', 'redis'
'lifetime' => 120, // minutes

// Ensure session middleware is active
// config/middleware.php
'web' => [
    StartSession::class,
    // ...
],
```

### Token Exchange Exception

**Error**: `Access token not found in provider response`

**Causes:**
1. Invalid client credentials
2. Redirect URI mismatch
3. Authorization code already used
4. Provider API error

**Solutions:**
```php
// Verify credentials match provider console
GOOGLE_CLIENT_ID=correct-client-id
GOOGLE_CLIENT_SECRET=correct-secret

// Verify redirect URI exactly matches
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/socialite/google/callback

// Check provider console for errors
// Enable debug logging
logger()->debug('OAuth error', [
    'provider' => 'google',
    'request' => $request->all(),
]);
```

### User Data Exception

**Error**: Failed to fetch user data

**Causes:**
1. Invalid access token
2. Insufficient scopes
3. Provider API rate limit
4. Token expired

**Solutions:**
```php
// Check required scopes
'google' => [
    'scopes' => [
        'openid',
        'profile',
        'email', // Required for email access
    ],
],

// Check token validity
if ($account->isTokenExpired()) {
    // Refresh or re-authenticate
}
```

### Redirect URI Mismatch

**Error**: `redirect_uri_mismatch`

**Solution:**
1. Ensure redirect URI in `.env` **exactly** matches provider console
2. Include protocol (`http://` or `https://`)
3. Include port if not default (`:8000`)
4. No trailing slash unless configured in provider

```env
# Correct
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/socialite/google/callback

# Incorrect
GOOGLE_REDIRECT_URI=localhost:8000/auth/socialite/google/callback  # Missing protocol
GOOGLE_REDIRECT_URI=http://localhost/auth/socialite/google/callback  # Missing port
```

## Testing

### Unit Testing

```php
use Toporia\Socialite\SocialiteManager;
use Toporia\Socialite\Contracts\ProviderInterface;
use Toporia\Socialite\User;
use PHPUnit\Framework\TestCase;

class SocialiteTest extends TestCase
{
    public function testSocialiteDriver()
    {
        $mockProvider = $this->createMock(ProviderInterface::class);
        $mockProvider->expects($this->once())
            ->method('user')
            ->willReturn(new User(
                id: '123',
                name: 'Test User',
                email: 'test@example.com',
                avatar: 'https://example.com/avatar.jpg',
                nickname: 'testuser'
            ));

        $manager = new SocialiteManager($container, $httpClient, []);
        $manager->extend('test', fn() => $mockProvider);

        $user = $manager->driver('test')->user($request);

        $this->assertEquals('123', $user->id);
        $this->assertEquals('Test User', $user->name);
    }
}
```

### Integration Testing

```php
use Toporia\Socialite\Models\SocialAccount;

class SocialAccountTest extends TestCase
{
    public function testTokenExpiration()
    {
        $account = SocialAccount::create([
            'user_id' => 1,
            'provider' => 'google',
            'provider_id' => '123',
            'provider_expires_at' => now()->subHour(),
        ]);

        $this->assertTrue($account->isTokenExpired());
    }

    public function testRefreshTokenAvailability()
    {
        $account = SocialAccount::create([
            'user_id' => 1,
            'provider' => 'google',
            'provider_id' => '123',
            'provider_refresh_token' => 'refresh-token',
        ]);

        $this->assertTrue($account->hasRefreshToken());
    }
}
```

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-provider`)
3. Add tests for new features
4. Commit your changes (`git commit -m 'Add Discord provider'`)
5. Push to the branch (`git push origin feature/new-provider`)
6. Open a Pull Request

### Adding New Providers

To add a new OAuth provider:

1. Create provider class extending `AbstractProvider`
2. Implement required methods: `getAuthUrl()`, `getTokenUrl()`, `getUserUrl()`, `mapUserToObject()`
3. Add configuration to `config/socialite.php`
4. Add tests
5. Update documentation

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

- **Documentation**: [https://github.com/Minhphung7820/toporia/blob/main/docs/SOCIALITE.md](https://github.com/Minhphung7820/toporia/blob/main/docs/SOCIALITE.md)
- **Issues**: [https://github.com/Minhphung7820/toporia/issues](https://github.com/Minhphung7820/toporia/issues)
- **Email**: minhphung485@gmail.com

## Resources

### Provider Documentation
- [Google OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)
- [Facebook Login](https://developers.facebook.com/docs/facebook-login)
- [GitHub OAuth Apps](https://docs.github.com/en/developers/apps/building-oauth-apps)
- [Twitter OAuth 2.0](https://developer.twitter.com/en/docs/authentication/oauth-2-0)
- [LinkedIn OAuth 2.0](https://docs.microsoft.com/en-us/linkedin/shared/authentication/authentication)

### OAuth Console Links
- [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
- [Facebook App Dashboard](https://developers.facebook.com/apps/)
- [GitHub Developer Settings](https://github.com/settings/developers)
- [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard)
- [LinkedIn Developer Portal](https://www.linkedin.com/developers/apps)

---

**Built with care by the Toporia Framework team.**
