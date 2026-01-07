<?php

namespace Scwar\Monnify\Tests\Unit\Models;

use Orchestra\Testbench\TestCase;
use Scwar\Monnify\Models\MonnifyTransaction;

class MonnifyTransactionTest extends TestCase
{
    /**
     * Test transaction model exists.
     *
     * @return void
     */
    public function test_transaction_model_exists(): void
    {
        $transaction = new MonnifyTransaction();

        $this->assertInstanceOf(MonnifyTransaction::class, $transaction);
    }
}
