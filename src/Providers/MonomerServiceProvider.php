<?php

namespace ArtisanCloud\SaaSMonomer\Providers;

use ArtisanCloud\SaaSMonomer\Services\TeamService\src\Providers\TeamServiceProvider;
use ArtisanCloud\SaaSMonomer\Services\UserService\src\Providers\UserServiceProvider;
use ArtisanCloud\SaaSMonomer\Console\Commands\SaasMonomerInstallCommand;
use Laravel\Passport\Passport;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

/**
 * Class MonomerServiceProvider
 * @package ArtisanCloud\SaaSMonomer\Providers
 */
class MonomerServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
//        $this->app->register(
//            TenantServiceProvider::class
//        );
        $this->app->register(TeamServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        
        if ($this->app->runningInConsole()) {

//            $this->commands([
//                SaasMonomerInstallCommand::class,
//            ]);

            $this->publishes([
                __DIR__ . '/../../config/monomer.php' => "/../" . config_path('artisancloud/monomer.php'),
            ], ['ArtisanCloud','SaaSMonomer', 'Landlord-Config']);

        }
    }
}
