<?php

namespace ArtisanCloud\SaaSMonomer\Providers;

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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        // make sure passport is installed
        Passport::routes(function ($router) {
            // RouteRegistrar->forAccessTokens()
            $router->forAccessTokens();
        }, ['middleware' => 'checkHeader']);
        Passport::tokensExpireIn(now()->addDays(90));
        Passport::refreshTokensExpireIn(now()->addDays(90));



        if ($this->app->runningInConsole()) {

//            $this->commands([
//                SaasMonomerInstallCommand::class,
//            ]);

            $this->publishes([
                __DIR__ . '/../../config/monomer.php' => "/../" . config_path('artisancloud/monomer.php'),
            ], ['SaaSMonomer', 'Landlord-Config']);

        }
    }
}
