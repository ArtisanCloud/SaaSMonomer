<?php

namespace ArtisanCloud\SaaSMonomer\Services\UserService\src\Providers;

use Illuminate\Support\ServiceProvider;
use ArtisanCloud\SaaSFramework\Services\UserService\src\Contracts\UserServiceContract;
use ArtisanCloud\SaaSFramework\Services\UserService\src\UserService;

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
        $this->app->bind(
            UserServiceContract::class,
            UserService::class
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
                  __DIR__ . '/../../config/user.php' => "/../" . config_path('artisancloud/user.php'),
              ], ['SaaSFramework', 'User-Config']);
            }

    }
}
