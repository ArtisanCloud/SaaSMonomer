<?php

namespace ArtisanCloud\SaaSFramework\Services\UserService\src\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class UserService
 * @package ArtisanCloud\SaaSFramework\Services\UserService\src
 */
class UserService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return UserService::class;
    }
}
