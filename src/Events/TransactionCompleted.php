<?php

namespace Scwar\Monnify\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Scwar\Monnify\Dto\TransactionResponse;

class TransactionCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \Scwar\Monnify\Dto\TransactionResponse  $transaction
     * @return void
     */
    public function __construct(
        public TransactionResponse $transaction
    ) {
    }
}
