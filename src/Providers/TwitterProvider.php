<?php

declare(strict_types=1);

namespace Toporia\Socialite\Providers;

use Toporia\Socialite\AbstractProvider;
use Toporia\Socialite\User;

/**
 * Class TwitterProvider
 *
 * Twitter/X OAuth 2.0 Provider
 *
 * Note: Twitter deprecated OAuth 1.0a. This implements OAuth 2.0 with PKCE.
 * Requires Twitter API v2 credentials.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
final class TwitterProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(): string
    {
        return 'https://twitter.com/i/oauth2/authorize';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return 'https://api.twitter.com/2/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserUrl(): string
    {
        return 'https://api.twitter.com/2/users/me?user.fields=id,name,username,profile_image_url';
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        // Twitter API v2 response format
        $data = $user['data'] ?? $user;

        return new User(
            id: (string) ($data['id'] ?? ''),
            name: $data['name'] ?? '',
            email: '', // Twitter OAuth 2.0 doesn't provide email by default
            avatar: $data['profile_image_url'] ?? null,
            nickname: $data['username'] ?? null,
            attributes: $user
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function formatScopes(): string
    {
        // Twitter uses space-separated scopes
        return implode(' ', $this->scopes);
    }
}
