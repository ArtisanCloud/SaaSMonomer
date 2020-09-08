<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TeamService\src\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSFramework\Services\TeamService\src\Contracts\TeamServiceContract;
use ArtisanCloud\SaaSFramework\Services\TeamService\src\TeamService;

/**
 * Class TeamServiceProvider
 * @package App\Providers
 */
class TeamServiceProvider extends ServiceProvider
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
            TeamServiceContract::class,
            TeamService::class
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
//                  __DIR__ . '/../../config/team.php' => "/../" . config_path('artisancloud/team.php'),
//              ], ['SaaSFramework', 'Team-Config']);

              // register artisan command
              if (! class_exists('CreateTeamTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_teams_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_teams_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud','SaaSMonomer', 'Team-Migration']);
              }
            }

    }
}
