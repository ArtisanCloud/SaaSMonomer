<?php

namespace ArtisanCloud\SaaSMonomer\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSMonomer\Contracts\UserServiceContract;
use ArtisanCloud\SaaSMonomer\UserService;

/**
 * Class UserServiceProvider
 * @package App\Providers
 */
class UserServiceProvider extends ServiceProvider
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
        $this->app->bind(
            UserServiceContract::class,
            UserService::class
        );
    }
}
