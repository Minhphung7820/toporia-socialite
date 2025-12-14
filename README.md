# Toporia Socialite

OAuth social authentication for Toporia Framework.

## Installation

```bash
composer require toporia/socialite
```

## Setup

### 1. Register Service Provider

Add to `bootstrap/app.php` or `App/Infrastructure/Providers/AppServiceProvider.php`:

```php
// bootstrap/app.php - trong RegisterProviders::bootstrap()
$app->registerProviders([
    // ... other providers
    \Toporia\Socialite\SocialiteServiceProvider::class,
]);

// Hoặc trong AppServiceProvider
public function register(ContainerInterface $container): void
{
    $container->register(\Toporia\Socialite\SocialiteServiceProvider::class);
}
```

### 2. Publish Config (optional)

```bash
php console vendor:publish --tag=socialite-config
```

Or manually copy `vendor/toporia/socialite/config/socialite.php` to `config/socialite.php`.

### 3. Configure OAuth Providers

Add to your `.env` file:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=/auth/socialite/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=/auth/socialite/facebook/callback

# GitHub OAuth
GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
GITHUB_REDIRECT_URI=/auth/socialite/github/callback
```

## Usage

### 1. Redirect to Provider

```php
use Toporia\Socialite\SocialiteManager;

class AuthController
{
    public function __construct(
        private SocialiteManager $socialite
    ) {}

    public function redirectToGoogle()
    {
        return $this->socialite->driver('google')->redirect();
    }

    public function redirectToFacebook()
    {
        return $this->socialite->driver('facebook')->redirect();
    }

    public function redirectToGithub()
    {
        return $this->socialite->driver('github')->redirect();
    }
}
```

### 2. Handle Callback

```php
public function handleGoogleCallback()
{
    $user = $this->socialite->driver('google')->user();

    // $user->getId()
    // $user->getName()
    // $user->getEmail()
    // $user->getAvatar()

    // Find or create user in your database
    $existingUser = UserModel::where('email', $user->getEmail())->first();

    if ($existingUser) {
        auth()->login($existingUser);
    } else {
        $newUser = UserModel::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar(),
            'provider' => 'google',
            'provider_id' => $user->getId(),
        ]);
        auth()->login($newUser);
    }

    return redirect('/dashboard');
}
```

### 3. Helper Function

```php
// Get socialite manager
$manager = socialite();

// Get specific driver
$google = socialite('google');

// Redirect
return socialite('github')->redirect();

// Get user
$user = socialite('github')->user();
```

### 4. Store Social Account

Use the included `SocialAccount` model to track linked social accounts:

```php
use Toporia\Socialite\Models\SocialAccount;

// After OAuth callback
$socialUser = socialite('google')->user();

$socialAccount = SocialAccount::updateOrCreate(
    [
        'provider' => 'google',
        'provider_id' => $socialUser->getId(),
    ],
    [
        'user_id' => $user->id,
        'name' => $socialUser->getName(),
        'email' => $socialUser->getEmail(),
        'avatar' => $socialUser->getAvatar(),
        'token' => $socialUser->getToken(),
        'refresh_token' => $socialUser->getRefreshToken(),
    ]
);
```

## Supported Providers

- Google
- Facebook
- GitHub

## Adding Custom Providers

Extend `AbstractProvider` to add custom OAuth providers:

```php
use Toporia\Socialite\AbstractProvider;
use Toporia\Socialite\User;

class TwitterProvider extends AbstractProvider
{
    protected function getAuthUrl(): string
    {
        return 'https://twitter.com/oauth/authorize';
    }

    protected function getTokenUrl(): string
    {
        return 'https://api.twitter.com/oauth/access_token';
    }

    protected function getUserByToken(string $token): array
    {
        // Fetch user data from Twitter API
    }

    protected function mapUserToObject(array $user): User
    {
        return new User([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'] ?? null,
            'avatar' => $user['profile_image_url'] ?? null,
        ]);
    }
}
```

## Configuration

```php
// config/socialite.php
return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/socialite/google/callback'),
        'scopes' => ['openid', 'profile', 'email'],
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', ''),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),
        'redirect' => env('FACEBOOK_REDIRECT_URI', '/auth/socialite/facebook/callback'),
        'scopes' => ['email', 'public_profile'],
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID', ''),
        'client_secret' => env('GITHUB_CLIENT_SECRET', ''),
        'redirect' => env('GITHUB_REDIRECT_URI', '/auth/socialite/github/callback'),
        'scopes' => ['user:email'],
    ],
];
```

## License

MIT
