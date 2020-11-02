<?php

namespace App\Services\PaymentService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class PaymentService
 * @package App\Services\PaymentService
 */
class PaymentService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return PaymentService::class;
    }
}
