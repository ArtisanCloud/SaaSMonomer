<?php

namespace App\Services\QuotationService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class QuotationService
 * @package App\Services\QuotationService
 */
class QuotationService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return QuotationService::class;
    }
}
