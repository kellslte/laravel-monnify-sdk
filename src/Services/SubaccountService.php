<?php

namespace Scwar\Monnify\Services;

use Illuminate\Support\Facades\Http;
use Scwar\Monnify\Exceptions\RequestException;

class SubaccountService
{
    /**
     * Create a new SubaccountService instance.
     *
     * @param  \Scwar\Monnify\Services\AuthService  $authService
     * @param  string  $baseUrl
     * @return void
     */
    public function __construct(
        protected AuthService $authService,
        protected string $baseUrl
    ) {
    }

    /**
     * Create a subaccount.
     *
     * @param  array  $data
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function create(array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/api/v1/subaccounts', $data);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to create subaccount: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to create subaccount',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * Update a subaccount.
     *
     * @param  string  $subaccountCode
     * @param  array  $data
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function update(string $subaccountCode, array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->put($this->baseUrl.'/api/v1/subaccounts/'.$subaccountCode, $data);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to update subaccount: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to update subaccount',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * Delete a subaccount.
     *
     * @param  string  $subaccountCode
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function delete(string $subaccountCode): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->delete($this->baseUrl.'/api/v1/subaccounts/'.$subaccountCode);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to delete subaccount: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to delete subaccount',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * Get subaccount details.
     *
     * @param  string  $subaccountCode
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function getDetails(string $subaccountCode): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl.'/api/v1/subaccounts/'.$subaccountCode);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to get subaccount details: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to get subaccount details',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * List subaccounts.
     *
     * @param  array  $filters
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function list(array $filters = []): array
    {
        $queryString = http_build_query($filters);
        $url = $this->baseUrl.'/api/v1/subaccounts?'.$queryString;

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($url);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to list subaccounts: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to list subaccounts',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }
}
