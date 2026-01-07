<?php

namespace Scwar\Monnify\Services;

use Illuminate\Support\Facades\Http;
use Scwar\Monnify\Dto\InvoiceResponse;
use Scwar\Monnify\Exceptions\RequestException;

class InvoiceService
{
    /**
     * Create a new InvoiceService instance.
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
     * Create an invoice.
     *
     * @param  array  $data
     * @return \Scwar\Monnify\Dto\InvoiceResponse
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function create(array $data): InvoiceResponse
    {
        // Merge contract code if not provided
        if (! isset($data['contractCode'])) {
            $data['contractCode'] = $this->contractCode;
        }

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/api/v1/invoice/create', $data);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to create invoice: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to create invoice',
                $response->status(),
                $responseData
            );
        }

        return InvoiceResponse::fromArray($responseData);
    }

    /**
     * Get invoice details.
     *
     * @param  string  $invoiceReference
     * @return \Scwar\Monnify\Dto\InvoiceResponse
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function getDetails(string $invoiceReference): InvoiceResponse
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl.'/api/v1/invoice/'.$invoiceReference);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to get invoice details: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to get invoice details',
                $response->status(),
                $responseData
            );
        }

        return InvoiceResponse::fromArray($responseData);
    }

    /**
     * Cancel an invoice.
     *
     * @param  string  $invoiceReference
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function cancel(string $invoiceReference): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->delete($this->baseUrl.'/api/v1/invoice/'.$invoiceReference.'/cancel');

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to cancel invoice: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to cancel invoice',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }

    /**
     * List invoices.
     *
     * @param  array  $filters
     * @return array
     *
     * @throws \Scwar\Monnify\Exceptions\RequestException
     */
    public function list(array $filters = []): array
    {
        $queryString = http_build_query($filters);
        $url = $this->baseUrl.'/api/v1/invoice/all?'.$queryString;

        $response = Http::withHeaders([
            'Authorization' => $this->authService->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
        ])->get($url);

        if (! $response->successful()) {
            throw new RequestException(
                'Failed to list invoices: '.$response->body(),
                $response->status(),
                $response->json()
            );
        }

        $responseData = $response->json();

        if (! $responseData['requestSuccessful'] ?? false) {
            throw new RequestException(
                $responseData['responseMessage'] ?? 'Failed to list invoices',
                $response->status(),
                $responseData
            );
        }

        return $responseData['responseBody'] ?? $responseData;
    }
}
