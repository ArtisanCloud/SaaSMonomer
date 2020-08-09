<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TenantService
 * @package ArtisanCloud\SaaSMonomer\Services\TenantService\src
 */
class TenantService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return TenantService::class;
    }
}
