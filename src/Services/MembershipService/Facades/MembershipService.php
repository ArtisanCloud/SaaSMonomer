<?php

namespace App\Services\MembershipService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class MembershipService
 * @package App\Services\MembershipService
 */
class MembershipService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return MembershipService::class;
    }
}
