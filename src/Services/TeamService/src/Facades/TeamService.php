<?php

namespace ArtisanCloud\SaaSFramework\Services\TeamService\src\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TeamService
 * @package ArtisanCloud\SaaSFramework\Services\TeamService\src
 */
class TeamService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return TeamService::class;
    }
}
