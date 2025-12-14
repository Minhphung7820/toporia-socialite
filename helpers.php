<?php

declare(strict_types=1);

/**
 * Socialite Helper Functions
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 */

use Toporia\Socialite\SocialiteManager;

if (!function_exists('socialite')) {
    /**
     * Get the socialite manager or a specific driver.
     *
     * @param string|null $driver
     * @return SocialiteManager|mixed
     */
    function socialite(?string $driver = null): mixed
    {
        $manager = app(SocialiteManager::class);

        if ($driver !== null) {
            return $manager->driver($driver);
        }

        return $manager;
    }
}
