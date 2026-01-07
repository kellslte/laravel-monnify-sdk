<?php

namespace Scwar\Monnify\Services;

use Scwar\Monnify\Contracts\MonnifyInterface;

class MonnifyService implements MonnifyInterface
{
    /**
     * Create a new MonnifyService instance.
     *
     * @param  \Scwar\Monnify\Services\AuthService  $authService
     * @param  \Scwar\Monnify\Services\TransactionService  $transactionService
     * @param  \Scwar\Monnify\Services\InvoiceService  $invoiceService
     * @param  \Scwar\Monnify\Services\SubaccountService  $subaccountService
     * @return void
     */
    public function __construct(
        protected AuthService $authService,
        protected TransactionService $transactionService,
        protected InvoiceService $invoiceService,
        protected SubaccountService $subaccountService
    ) {
    }

    /**
     * Get the transaction service instance.
     *
     * @return \Scwar\Monnify\Services\TransactionService
     */
    public function transaction(): TransactionService
    {
        return $this->transactionService;
    }

    /**
     * Get the invoice service instance.
     *
     * @return \Scwar\Monnify\Services\InvoiceService
     */
    public function invoice(): InvoiceService
    {
        return $this->invoiceService;
    }

    /**
     * Get the subaccount service instance.
     *
     * @return \Scwar\Monnify\Services\SubaccountService
     */
    public function subaccount(): SubaccountService
    {
        return $this->subaccountService;
    }
}
