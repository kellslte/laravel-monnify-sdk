<?php

namespace Scwar\Monnify\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use Scwar\Monnify\Dto\InvoiceResponse;

class InvoiceResponseTest extends TestCase
{
    /**
     * Monnify returns createdOn/updatedOn as date strings, not arrays --
     * typing them as ?array would throw a TypeError against a real response.
     */
    public function test_created_on_and_updated_on_accept_date_strings(): void
    {
        $invoice = InvoiceResponse::fromArray([
            'responseBody' => [
                'invoiceReference' => 'INV-1',
                'createdOn' => '2024-01-01 10:00:00',
                'updatedOn' => '2024-01-01 10:05:00',
            ],
        ]);

        $this->assertSame('2024-01-01 10:00:00', $invoice->createdOn);
        $this->assertSame('2024-01-01 10:05:00', $invoice->updatedOn);
    }

    public function test_amount_paid_is_captured_separately_from_amount(): void
    {
        $invoice = InvoiceResponse::fromArray([
            'responseBody' => [
                'invoiceReference' => 'INV-1',
                'amount' => 5100.0,
                'amountPaid' => 2500.0,
            ],
        ]);

        $this->assertSame(5100.0, $invoice->amount);
        $this->assertSame(2500.0, $invoice->amountPaid);
    }
}
