<?php

declare(strict_types=1);

namespace Toporia\Socialite\Providers;

use Toporia\Socialite\AbstractProvider;
use Toporia\Socialite\Exceptions\TokenExchangeException;
use Toporia\Socialite\User;

/**
 * Class GitHubProvider
 *
 * GitHub OAuth Provider
 *
 * GitHub has specific requirements:
 * - Token endpoint requires Accept: application/json header to return JSON
 * - Without this header, GitHub returns form-encoded data (access_token=xxx&token_type=bearer)
 * - Email may not be included in user data if user has private email settings
 *
 * @author      Phungtruong7820 <minhphung485@gmail.com>
 * @copyright   Copyright (c) 2025 Toporia Framework
 * @license     MIT
 * @version     1.0.0
 * @package     toporia/framework
 * @subpackage  Socialite\Providers
 * @since       2025-01-10
 *
 * @link        https://github.com/Minhphung7820/toporia
 */
final class GitHubProvider extends AbstractProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl(): string
    {
        return 'https://github.com/login/oauth/authorize';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl(): string
    {
        return 'https://github.com/login/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserUrl(): string
    {
        return 'https://api.github.com/user';
    }

    /**
     * Exchange authorization code for access token.
     *
     * GitHub requires Accept: application/json header to return JSON response.
     * Without this header, GitHub returns form-encoded data which cannot be parsed as JSON.
     *
     * @param string $code Authorization code
     * @return string Access token
     * @throws TokenExchangeException If token exchange fails
     */
    protected function exchangeCodeForToken(string $code): string
    {
        $response = $this->httpClient
            ->asForm()
            ->acceptJson()
            ->post($this->getTokenUrl(), [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUrl,
            ]);

        if (!$response->successful()) {
            throw TokenExchangeException::fromStatusCode($response->status());
        }

        $data = $response->json();

        // Check for error in response (GitHub returns errors in JSON body with 200 status)
        if (isset($data['error'])) {
            $errorMessage = $data['error_description'] ?? $data['error'];
            throw new TokenExchangeException($errorMessage);
        }

        if (!isset($data['access_token'])) {
            throw new TokenExchangeException('Access token not found in GitHub response.');
        }

        return $data['access_token'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUserFromToken(string $token): User
    {
        $user = $this->getUserData($token);

        // Get email separately if not included (user may have private email settings)
        if (empty($user['email'])) {
            $user['email'] = $this->fetchPrimaryEmail($token);
        }

        return $this->mapUserToObject($user);
    }

    /**
     * Fetch primary email from GitHub emails endpoint.
     *
     * GitHub users can have their email set to private, in which case
     * the /user endpoint doesn't return the email. We need to fetch
     * it from the /user/emails endpoint with user:email scope.
     *
     * @param string $token Access token
     * @return string|null Primary email or null if not found
     */
    private function fetchPrimaryEmail(string $token): ?string
    {
        $response = $this->httpClient
            ->withToken($token)
            ->acceptJson()
            ->get('https://api.github.com/user/emails');

        if (!$response->successful()) {
            return null;
        }

        $emails = $response->json();

        if (!is_array($emails)) {
            return null;
        }

        // Find primary email
        foreach ($emails as $emailData) {
            if (($emailData['primary'] ?? false) && ($emailData['verified'] ?? false)) {
                return $emailData['email'];
            }
        }

        // Fallback: return first verified email
        foreach ($emails as $emailData) {
            if ($emailData['verified'] ?? false) {
                return $emailData['email'];
            }
        }

        // Last resort: return first email
        return $emails[0]['email'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user): User
    {
        return new User(
            id: (string) ($user['id'] ?? ''),
            name: $user['name'] ?? $user['login'] ?? '',
            email: $user['email'] ?? '',
            avatar: $user['avatar_url'] ?? null,
            nickname: $user['login'] ?? null,
            attributes: $user
        );
    }
}
