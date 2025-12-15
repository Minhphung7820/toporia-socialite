<?php

declare(strict_types=1);

namespace Toporia\Socialite\Exceptions;

/**
 * Exception thrown when OAuth state parameter is invalid or missing.
 *
 * This indicates a potential CSRF attack or session issue.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */
class InvalidStateException extends SocialiteException
{
}
