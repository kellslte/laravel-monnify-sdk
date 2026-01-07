<?php

namespace Scwar\Monnify\Dto;

class InvoiceResponse
{
    /**
     * Create a new invoice response instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(
        public readonly ?string $invoiceReference = null,
        public readonly ?string $invoiceStatus = null,
        public readonly ?string $checkoutUrl = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?array $lineItems = null,
        public readonly ?string $customerName = null,
        public readonly ?string $customerEmail = null,
        public readonly ?array $metadata = null,
        public readonly ?string $expiryDate = null,
        public readonly ?array $createdOn = null,
        public readonly ?array $updatedOn = null,
        public readonly ?array $raw = null
    ) {
    }

    /**
     * Create an invoice response from array data.
     *
     * @param  array  $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $responseBody = $data['responseBody'] ?? $data;

        return new self(
            invoiceReference: $responseBody['invoiceReference'] ?? null,
            invoiceStatus: $responseBody['invoiceStatus'] ?? null,
            checkoutUrl: $responseBody['checkoutUrl'] ?? null,
            amount: $responseBody['amount'] ?? null,
            currency: $responseBody['currency'] ?? null,
            lineItems: $responseBody['lineItems'] ?? null,
            customerName: $responseBody['customerName'] ?? null,
            customerEmail: $responseBody['customerEmail'] ?? null,
            metadata: $responseBody['metadata'] ?? null,
            expiryDate: $responseBody['expiryDate'] ?? null,
            createdOn: $responseBody['createdOn'] ?? null,
            updatedOn: $responseBody['updatedOn'] ?? null,
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
            'invoiceReference' => $this->invoiceReference,
            'invoiceStatus' => $this->invoiceStatus,
            'checkoutUrl' => $this->checkoutUrl,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'lineItems' => $this->lineItems,
            'customerName' => $this->customerName,
            'customerEmail' => $this->customerEmail,
            'metadata' => $this->metadata,
            'expiryDate' => $this->expiryDate,
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
        ];
    }
}
