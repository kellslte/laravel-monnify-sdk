<?php

namespace Scwar\Monnify\Services;

use Illuminate\Support\Facades\Http;
use Scwar\Monnify\Dto\TransactionResponse;
use Scwar\Monnify\Exceptions\RequestException;

class TransactionService
{
    /**
     * Create a new TransactionService instance.
     *
     * @param  \Scwar\Monnify\Services\AuthService  $authService
     * @param  string  $contractCode
     * @param  string  $baseUrl
     * @return void
     */
    public function __construct(
        protected AuthService $authService,
        protected string $contractCode,
        protected string $baseUrl
    ) {
    }

    /**
     * Initialize a transaction.
     *
     * @param  array  $data
     * @return \Scwar\Monnify\Dto\TransactionResponse
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function initialize(array $data): TransactionResponse
    {
        // Merge contract code if not provided
        if (! isset($data['contractCode'])) {
            $data['contractCode'] = $this->contractCode;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/api/v1/merchant/transactions/init-transaction', $data);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to initialize transaction: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to initialize transaction',
                $response->status(),
                $responseData
            );
        }

        return TransactionResponse::fromArray($responseData);
    }

    /**
     * Get transaction status.
     *
     * @param  string  $transactionReference
     * @return \Scwar\Monnify\Dto\TransactionResponse
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function getStatus(string $transactionReference): TransactionResponse
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl.'/api/v2/transactions/'.$transactionReference);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to get transaction status: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to get transaction status',
                $response->status(),
                $responseData
            );
        }

        return TransactionResponse::fromArray($responseData);
    }

    /**
     * Verify transaction.
     *
     * @param  string  $transactionReference
     * @return \Scwar\Monnify\Dto\TransactionResponse
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function verify(string $transactionReference): TransactionResponse
    {
        return $this->getStatus($transactionReference);
    }

    /**
     * Refund a transaction.
     *
     * @param  string  $transactionReference
     * @param  float  $amount
     * @param  string|null  $refundAmount
     * @param  string|null  $refundReason
     * @param  string|null  $customerNote
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function refund(
        string $transactionReference,
        float $amount,
        ?string $refundAmount = null,
        ?string $refundReason = null,
        ?string $customerNote = null
    ): array {
        $data = [
            'transactionReference' => $transactionReference,
            'amount' => $refundAmount ?? $amount,
        ];

        if ($refundReason) {
            $data['refundReason'] = $refundReason;
        }

        if ($customerNote) {
            $data['customerNote'] = $customerNote;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/api/v2/refunds', $data);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to refund transaction: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to refund transaction',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * Query transaction history.
     *
     * @param  array  $filters
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function queryHistory(array $filters = []): array
    {
        $queryString = http_build_query($filters);
        $url = $this->baseUrl.'/api/v1/merchant/transactions/search?'.$queryString;

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($url);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to query transaction history: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to query transaction history',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }
}
