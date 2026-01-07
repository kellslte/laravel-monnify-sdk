<?php

namespace Scwar\Monnify\Facades;

use Illuminate\Support\Facades\Facade;
use Scwar\Monnify\Services\InvoiceService;
use Scwar\Monnify\Services\MonnifyService as MonnifyServiceClass;
use Scwar\Monnify\Services\SubaccountService;
use Scwar\Monnify\Services\TransactionService;

/**
 * @method static TransactionService transaction()
 * @method static InvoiceService invoice()
 * @method static SubaccountService subaccount()
 *
 * @see \Scwar\Monnify\Services\MonnifyService
 */
class Monnify extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MonnifyServiceClass::class;
    }
}
