<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\LandlordService\src\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LandlordService
 * @package ArtisanCloud\SaaSMonomer\Services\LandlordService\src
 */
class LandlordService extends Facade
{
    //
    protected static function getFacadeAccessor()
    {
        return LandlordService::class;
    }
}
