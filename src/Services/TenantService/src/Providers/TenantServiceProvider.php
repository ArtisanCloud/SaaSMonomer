<?php

namespace ArtisanCloud\SaaSMonomer\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSMonomer\Contracts\TenantServiceContract;
use ArtisanCloud\SaaSMonomer\TenantService;

/**
 * Class TenantServiceProvider
 * @package App\Providers
 */
class TenantServiceProvider extends ServiceProvider
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
            TenantServiceContract::class,
            TenantService::class
        );
    }
}
