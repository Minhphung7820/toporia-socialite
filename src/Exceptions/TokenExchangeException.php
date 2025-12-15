<?php

declare(strict_types=1);

namespace Toporia\Socialite\Exceptions;

/**
 * Exception thrown when OAuth token exchange fails.
 *
 * This occurs during the code-for-token exchange step of OAuth 2.0 flow.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
class TokenExchangeException extends SocialiteException
{
    /**
     * Create exception with sanitized error message.
     *
     * @param int $statusCode HTTP status code from provider
     * @param \Throwable|null $previous Previous exception
     * @return self
     */
    public static function fromStatusCode(int $statusCode, ?\Throwable $previous = null): self
    {
        return new self(
            "Failed to exchange authorization code for access token (HTTP {$statusCode}). Check client credentials and redirect URI.",
            $statusCode,
            $previous
        );
    }
}
