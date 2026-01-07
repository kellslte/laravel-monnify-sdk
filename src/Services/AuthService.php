<?php

namespace Scwar\Monnify\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Scwar\Monnify\Exceptions\AuthenticationException;

class AuthService
{
    /**
     * The Monnify API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The Monnify secret key.
     *
     * @var string
     */
    protected $secretKey;

    /**
     * The Monnify base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Cache configuration.
     *
     * @var array
     */
    protected $cacheConfig;

    /**
     * Create a new AuthService instance.
     *
     * @param  string  $apiKey
     * @param  string  $secretKey
     * @param  string  $baseUrl
     * @param  array  $cacheConfig
     * @return void
     */
    public function __construct($apiKey, $secretKey, $baseUrl, $cacheConfig)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->baseUrl = $baseUrl;
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * Generate an access token.
     *
     * @param  bool  $forceRefresh
     * @return string
     *
     * @throws \Scwar\Monnify\Exceptions\AuthenticationException
     */
    public function getAccessToken($forceRefresh = false)
    {
        $cacheKey = $this->cacheConfig['token_key'] ?? 'monnify_access_token';
        $ttl = $this->cacheConfig['ttl'] ?? 3600;

        // Return cached token if available and not forcing refresh
        if (! $forceRefresh && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Generate new token
        $token = $this->generateToken();

        // Cache the token
        Cache::put($cacheKey, $token, $ttl);

        return $token;
    }

    /**
     * Generate a new access token from Monnify API.
     *
     * @return string
     *
     * @throws \Scwar\Monnify\Exceptions\AuthenticationException
     */
    protected function generateToken()
    {
        $credentials = base64_encode($this->apiKey.':'.$this->secretKey);

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$credentials,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/api/v1/auth/login');

        if (! $response->successful()) {
            throw new AuthenticationException(
                'Failed to authenticate with Monnify: '.$response->body(),
                $response->status()
            );
        }

        $data = $response->json();

        if (! isset($data['responseBody']['accessToken'])) {
            throw new AuthenticationException(
                'Invalid response from Monnify authentication endpoint'
            );
        }

        return $data['responseBody']['accessToken'];
    }

    /**
     * Clear the cached access token.
     *
     * @return bool
     */
    public function clearToken()
    {
        $cacheKey = $this->cacheConfig['token_key'] ?? 'monnify_access_token';

        return Cache::forget($cacheKey);
    }

    /**
     * Get the authorization header for API requests.
     *
     * @param  bool  $forceRefresh
     * @return string
     */
    public function getAuthorizationHeader($forceRefresh = false)
    {
        $token = $this->getAccessToken($forceRefresh);

        return 'Bearer '.$token;
    }
}
