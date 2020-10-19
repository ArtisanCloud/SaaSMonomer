<?php
declare(strict_types=1);

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
                __DIR__ . '/../../config/tenant.php' => "/../" . config_path('artisancloud/tenant.php'),
            ], ['ArtisanCloud', 'SaaSMonomer', 'Tenant-Config']);

            // register artisan command
            if (!class_exists('CreateTenantTable')) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_tenants_table.php' => database_path('migrations/0_0_0_0_create_tenants_table.php'),
                    __DIR__ . '/../../database/migrations/create_r_artisan_to_team_table.php' => database_path('migrations/0_0_0_0_create_r_artisan_to_team_table.php'),
                    __DIR__ . '/../../database/migrations/create_r_team_to_company_table.php' => database_path('migrations/0_0_0_0_create_r_team_to_company_table.php'),
                    // you can add any number of migrations here
                ], ['ArtisanCloud', 'SaaSMonomer', 'Tenant-Migration']);
            }
        }

    }
}
