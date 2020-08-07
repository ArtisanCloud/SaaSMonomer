<?php

namespace ArtisanCloud\SaaSMonomer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TenantService
 * @package ArtisanCloud\SaaSMonomer
 */
class TenantService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return TenantService::class;
    }
}
