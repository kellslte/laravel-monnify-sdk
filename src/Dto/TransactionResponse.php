<?php

namespace Scwar\Monnify\Dto;

class TransactionResponse
{
    /**
     * Create a new transaction response instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(
        public readonly ?string $transactionReference = null,
        public readonly ?string $paymentReference = null,
        public readonly ?string $merchantName = null,
        public readonly ?string $apiKey = null,
        public readonly ?string $enabledPaymentMethod = null,
        public readonly ?string $checkoutUrl = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $contractCode = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $customerName = null,
        public readonly ?string $expiryDate = null,
        public readonly ?string $status = null,
        public readonly ?array $metaData = null,
        public readonly ?array $provider = null,
        public readonly ?array $createdOn = null,
        public readonly ?array $completedOn = null,
        public readonly ?array $raw = null
    ) {
    }

    /**
     * Create a transaction response from array data.
     *
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $responseBody = $data['responseBody'] ?? $data;

        return new self(
            transactionReference: $responseBody['transactionReference'] ?? null,
            paymentReference: $responseBody['paymentReference'] ?? null,
            merchantName: $responseBody['merchantName'] ?? null,
            apiKey: $responseBody['apiKey'] ?? null,
            enabledPaymentMethod: $responseBody['enabledPaymentMethod'] ?? null,
            checkoutUrl: $responseBody['checkoutUrl'] ?? null,
            amount: $responseBody['amount'] ?? null,
            currency: $responseBody['currency'] ?? null,
            contractCode: $responseBody['contractCode'] ?? null,
            customerEmail: $responseBody['customerEmail'] ?? null,
            customerName: $responseBody['customerName'] ?? null,
            expiryDate: $responseBody['expiryDate'] ?? null,
            status: $responseBody['status'] ?? null,
            metaData: $responseBody['metaData'] ?? null,
            provider: $responseBody['provider'] ?? null,
            createdOn: $responseBody['createdOn'] ?? null,
            completedOn: $responseBody['completedOn'] ?? null,
            raw: $data
        );
    }

    /**
     * Convert the response to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'transactionReference' => $this->transactionReference,
            'paymentReference' => $this->paymentReference,
            'merchantName' => $this->merchantName,
            'apiKey' => $this->apiKey,
            'enabledPaymentMethod' => $this->enabledPaymentMethod,
            'checkoutUrl' => $this->checkoutUrl,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'contractCode' => $this->contractCode,
            'customerEmail' => $this->customerEmail,
            'customerName' => $this->customerName,
            'expiryDate' => $this->expiryDate,
            'status' => $this->status,
            'metaData' => $this->metaData,
            'provider' => $this->provider,
            'createdOn' => $this->createdOn,
            'completedOn' => $this->completedOn,
        ];
    }
}
