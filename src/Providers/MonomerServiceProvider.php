<?php

namespace ArtisanCloud\SaaSMonomer\Providers;

use ArtisanCloud\SaaSMonomer\Console\Commands\SaaSMonomerCommand;
use Illuminate\Support\ServiceProvider;
use ArtisanCloud\TestService\Contracts\TestServiceContract;
use ArtisanCloud\TestService\TestService;
use Laravel\Passport\Passport;

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
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        // make sure passport is installed
        Passport::routes();

        if ($this->app->runningInConsole()) {
            // publish config file

            if ($this->app->runningInConsole()) {

//                $this->publishes([
//                    __DIR__ . '/../'.SaaSMonomerCommand::FOLDER_MIGRATION.'/migrations' => "/../" . app_path(),
//                ], 'saas-monomer-migrations');
            }

        }

//        $this->app->bind(
//            TestServiceContract::class,
//            TestService::class
//        );
    }
}
