<?php

namespace App\Services\OrderService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class OrderService
 * @package App\Services\OrderService
 */
class OrderService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return OrderService::class;
    }
}
