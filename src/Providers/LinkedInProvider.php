<?php

declare(strict_types=1);

namespace Toporia\Socialite\Providers;

use Toporia\Socialite\AbstractProvider;
use Toporia\Socialite\User;

/**
 * Class LinkedInProvider
 *
 * LinkedIn OAuth 2.0 Provider
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
final class LinkedInProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(): string
    {
        return 'https://www.linkedin.com/oauth/v2/authorization';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return 'https://www.linkedin.com/oauth/v2/accessToken';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserUrl(): string
    {
        return 'https://api.linkedin.com/v2/userinfo';
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return new User(
            id: (string) ($user['sub'] ?? ''), // LinkedIn uses 'sub' for user ID
            name: $user['name'] ?? '',
            email: $user['email'] ?? '',
            avatar: $user['picture'] ?? null,
            nickname: null, // LinkedIn doesn't provide username
            attributes: $user
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUserFromToken(string $token): User
    {
        $user = $this->getUserData($token);

        return $this->mapUserToObject($user);
    }

    /**
     * {@inheritdoc}
     */
    protected function formatScopes(): string
    {
        // LinkedIn uses space-separated scopes
        return implode(' ', $this->scopes);
    }
}
