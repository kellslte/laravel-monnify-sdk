<?php

namespace Scwar\Monnify\Contracts;

use Scwar\Monnify\Services\InvoiceService;
use Scwar\Monnify\Services\SubaccountService;
use Scwar\Monnify\Services\TransactionService;

interface MonnifyInterface
{
    /**
     * Get the transaction service instance.
     *
     * @return TransactionService
     */
    public function transaction(): TransactionService;

    /**
     * Get the invoice service instance.
     *
     * @return InvoiceService
     */
    public function invoice(): InvoiceService;

    /**
     * Get the subaccount service instance.
     *
     * @return SubaccountService
     */
    public function subaccount(): SubaccountService;
}
