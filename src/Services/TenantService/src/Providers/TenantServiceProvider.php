<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Contracts\TenantServiceContract;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;

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
        $this->app->bind(
            TenantServiceContract::class,
            TenantService::class
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
                  __DIR__ . '/../../config/tenant.php' => "/../" . config_path('tenant.php'),
              ], ['SaaSMonomer', 'Tenant-Model']);

              // register artisan command
              if (! class_exists('CreateTenantTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_tenants_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_tenants_table.php'),
                  // you can add any number of migrations here
                ], ['SaaSMonomer', 'Tenant-Migration']);
              }
            }

    }
}
