<?php

namespace Scwar\Monnify\Tests\Unit\Dto;

use PHPUnit\Framework\TestCase;
use Scwar\Monnify\Dto\TransactionResponse;

class TransactionResponseTest extends TestCase
{
    /**
     * Monnify returns createdOn/completedOn as date strings (e.g.
     * "2024-01-01 10:00:00"), not arrays. The webhook controller passes
     * these straight into Carbon::parse(), which requires a string --
     * typing them as ?array would make fromArray() throw a TypeError as
     * soon as a real Monnify response is decoded.
     */
    public function test_created_on_and_completed_on_accept_date_strings(): void
    {
        $transaction = TransactionResponse::fromArray([
            'responseBody' => [
                'transactionReference' => 'TXN-1',
                'createdOn' => '2024-01-01 10:00:00',
                'completedOn' => '2024-01-01 10:05:00',
            ],
        ]);

        $this->assertSame('2024-01-01 10:00:00', $transaction->createdOn);
        $this->assertSame('2024-01-01 10:05:00', $transaction->completedOn);
    }
}
