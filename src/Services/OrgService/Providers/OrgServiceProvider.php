<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OrgService\Contracts\OrgServiceContract;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;

/**
 * Class OrgServiceProvider
 * @package App\Providers
 */
class OrgServiceProvider extends ServiceProvider
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
            OrgServiceContract::class,
            OrgService::class
        );

        include_once(__DIR__.'/../config/constant.php');
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
//              $this->publishes([
//                  __DIR__ . '/../../config/org.php' => "/../" . config_path('org.php'),
//              ], ['ArtisanCloud', 'Org', 'Org-Config']);

              // publish migration file
//              $this->publishes([
//                  __DIR__ . '/../../config/org.php' => "/../" . config_path('org.php'),
//              ], ['ArtisanCloud', 'Org', 'Org-Model']);
              if (! class_exists('CreateOrgTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_orgs_table.php' => database_path('migrations/0_0_0_0_create_orgs_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud', 'Org', 'Org-Migration']);
              }
            }

    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/monomer.php' => "/../" . config_path('artisancloud/monomer.php'),
        ], ['ArtisanCloud','SaaSMonomer', 'Landlord-Config']);
    }

    protected function publishCommand()
    {
        $this->commands([
            Init::class,
            Migrate::class,
            SeedDemo::class
        ]);
    }
}
