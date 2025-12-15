<?php

declare(strict_types=1);

namespace Toporia\Socialite\Contracts;

/**
 * Interface for OAuth providers that support token refresh.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
interface RefreshableProviderInterface extends ProviderInterface
{
    /**
     * Refresh an expired access token using a refresh token.
     *
     * @param string $refreshToken The refresh token
     * @return array{access_token: string, refresh_token?: string, expires_in?: int} Token data
     */
    public function refreshToken(string $refreshToken): array;
}
