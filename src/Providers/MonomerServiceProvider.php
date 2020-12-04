<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Providers;

use App\Http\Kernel;
use ArtisanCloud\SaaSMonomer\Console\Commands\Tenant\CreateOrg;
use ArtisanCloud\SaaSMonomer\Console\Commands\Tenant\Init;
use ArtisanCloud\SaaSMonomer\Console\Commands\Tenant\Migrate;
use ArtisanCloud\SaaSMonomer\Console\Commands\Tenant\SeedDemo;
use ArtisanCloud\SaaSMonomer\Services\LandlordService\src\Providers\LandlordServiceProvider;

use ArtisanCloud\SaaSMonomer\Http\Middleware\{CheckLandlord, CheckTenant, CheckUser};

use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Providers\TenantServiceProvider;
use ArtisanCloud\SaaSMonomer\Services\TeamService\src\Providers\TeamServiceProvider;
use ArtisanCloud\SaaSMonomer\Services\UserService\src\Providers\UserServiceProvider;
use ArtisanCloud\SaaSMonomer\Console\Commands\SaasMonomerInstallCommand;
use Illuminate\Routing\Router;
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
        $this->app->register(LandlordServiceProvider::class);
        $this->app->register(TenantServiceProvider::class);
        $this->app->register(TeamServiceProvider::class);

        include_once(__DIR__.'/../../config/constant.php');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadTranslations();

        // config framework router
        $this->configRouter();

        if ($this->app->runningInConsole()) {

            $this->publishConfig();
            $this->publishCommand();

        }
    }

    public function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', USER_LANG);
    }


    public function configRouter()
    {

        // push middlewares
        $kernel = resolve(Kernel::class);
        $kernel->pushMiddleware(CheckLandlord::class);
        $kernel->pushMiddleware(CheckUser::class);

        // alias middlewares
        $router = resolve(Router::class);
        $router->aliasMiddleware('checkLandlord', CheckLandlord::class);
        $router->aliasMiddleware('checkUser', CheckUser::class);
        $router->aliasMiddleware('checkTenant', CheckTenant::class);

        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');

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
            SeedDemo::class,
            CreateOrg::class,
        ]);
    }
}
