<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\LandlordService\src\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSMonomer\Services\LandlordService\src\Contracts\LandlordServiceContract;
use ArtisanCloud\SaaSMonomer\Services\LandlordService\src\LandlordService;
use Laravel\Passport\Passport;

/**
 * Class LandlordServiceProvider
 * @package App\Providers
 */
class LandlordServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(
            LandlordServiceContract::class,
            LandlordService::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            // publish config file
            $this->publishes([
                __DIR__ . '/../../config/landlord.php' => "/../" . config_path('artisancloud/landlord.php'),
            ], ['ArtisanCloud', 'SaaSMonomer', 'Landlord-Config']);

            // register artisan command
            if (!class_exists('CreateLandlordTable')) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_landlords_table.php' => database_path('migrations/0_0_0_0_create_landlords_table.php'),
                    // you can add any number of migrations here
                ], ['ArtisanCloud', 'SaaSMonomer', 'Landlord-Migration']);
            }
        }

    }

}
