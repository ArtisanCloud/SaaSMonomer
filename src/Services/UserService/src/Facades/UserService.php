<?php

namespace ArtisanCloud\SaaSMonomer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class UserService
 * @package ArtisanCloud\SaaSMonomer
 */
class UserService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return UserService::class;
    }
}
