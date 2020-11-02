<?php

namespace App\Services\MembershipService\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MembershipService\Contracts\MembershipServiceContract;
use App\Services\MembershipService\MembershipService;

/**
 * Class MembershipServiceProvider
 * @package App\Providers
 */
class MembershipServiceProvider extends ServiceProvider
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
            MembershipServiceContract::class,
            MembershipService::class
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
//              $this->publishes([
//                  __DIR__ . '/../../config/membership.php' => "/../" . config_path('membership.php'),
//              ], ['ArtisanCloud', 'Membership', 'Membership-Config']);

              // publish migration file
//              $this->publishes([
//                  __DIR__ . '/../../config/membership.php' => "/../" . config_path('membership.php'),
//              ], ['ArtisanCloud', 'Membership', 'Membership-Model']);

              // register artisan command
              if (! class_exists('CreateMembershipTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_memberships_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_memberships_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud', 'Membership', 'Membership-Migration']);
              }
            }

    }
}
