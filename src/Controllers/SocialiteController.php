<?php

declare(strict_types=1);

namespace Toporia\Socialite\Controllers;

use Toporia\Framework\Http\{Request, RedirectResponse};
use Toporia\Framework\Session\Store;
use Toporia\Socialite\SocialiteManager;

/**
 * Class SocialiteController
 *
 * Handles OAuth authentication flows.
 * Uses framework's Store class for session management.
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 * @version     1.0.0
 * @package     toporia/framework
 * @subpackage  Socialite\Controllers
 * @since       2025-01-10
 *
 * @link        https://github.com/Minhphung7820/toporia
 */
final class SocialiteController
{
    /**
     * @param SocialiteManager $socialite Socialite manager
     * @param Store $session Session store
     */
    public function __construct(
        private SocialiteManager $socialite,
        private Store $session
    ) {}

    /**
     * Redirect to OAuth provider.
     *
     * @param Request $request HTTP request
     * @param string $provider Provider name
     * @return RedirectResponse
     */
    public function redirect(Request $request, string $provider): RedirectResponse
    {
        $driver = $this->socialite->driver($provider);
        $url = $driver->redirect($request);

        return new RedirectResponse($url);
    }

    /**
     * Handle OAuth callback.
     *
     * @param Request $request HTTP request
     * @param string $provider Provider name
     * @return RedirectResponse
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        try {
            $driver = $this->socialite->driver($provider);
            $user = $driver->user($request);

            // Store user data in session for application to handle
            $this->session->set('socialite_user', $user->toArray());
            $this->session->set('socialite_provider', $provider);

            // Validate and redirect to application's callback handler
            $redirectUrl = $request->input('redirect') ?? '/auth/socialite/success';
            $redirectUrl = $this->validateRedirectUrl($redirectUrl, '/auth/socialite/success');

            return new RedirectResponse($redirectUrl);
        } catch (\Throwable $e) {
            // Validate and redirect to error page
            $redirectUrl = $request->input('redirect_error') ?? '/auth/socialite/error';
            $redirectUrl = $this->validateRedirectUrl($redirectUrl, '/auth/socialite/error');

            $this->session->set('socialite_error', $e->getMessage());

            return new RedirectResponse($redirectUrl);
        }
    }

    /**
     * Validate redirect URL to prevent open redirect attacks.
     *
     * Only allows:
     * 1. Relative URLs starting with / (but not //)
     * 2. Whitelisted domains from config
     *
     * @param string $url URL to validate
     * @param string $fallback Fallback URL if validation fails
     * @return string Validated URL or fallback
     */
    private function validateRedirectUrl(string $url, string $fallback): string
    {
        // Allow relative URLs starting with / (but not // which is protocol-relative)
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return $url;
        }

        // Check against whitelist of allowed domains
        $parsed = parse_url($url);
        if ($parsed === false || !isset($parsed['host'])) {
            return $fallback;
        }

        $allowedDomains = config('socialite.allowed_redirect_domains', []);
        if (in_array($parsed['host'], $allowedDomains, true)) {
            return $url;
        }

        // If URL doesn't match any criteria, return fallback
        return $fallback;
    }
}

