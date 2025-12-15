<?php

declare(strict_types=1);

namespace Toporia\Socialite\Exceptions;

/**
 * Exception thrown when fetching user data from OAuth provider fails.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
class UserDataException extends SocialiteException
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
            "Failed to retrieve user data from OAuth provider (HTTP {$statusCode}). Token may be invalid or expired.",
            $statusCode,
            $previous
        );
    }
}
