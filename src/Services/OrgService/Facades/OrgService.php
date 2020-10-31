<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class OrgService
 * @package App\Services\OrgService
 */
class OrgService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return OrgService::class;
    }
}
