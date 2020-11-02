<?php

namespace App\Services\OrderService\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OrderService\Contracts\OrderServiceContract;
use App\Services\OrderService\OrderService;

/**
 * Class OrderServiceProvider
 * @package App\Providers
 */
class OrderServiceProvider extends ServiceProvider
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
            OrderServiceContract::class,
            OrderService::class
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
//                  __DIR__ . '/../../config/order.php' => "/../" . config_path('order.php'),
//              ], ['ArtisanCloud', 'Order', 'Order-Config']);

              // publish migration file
//              $this->publishes([
//                  __DIR__ . '/../../config/order.php' => "/../" . config_path('order.php'),
//              ], ['ArtisanCloud', 'Order', 'Order-Model']);

              // register artisan command
              if (! class_exists('CreateOrderTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_orders_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_orders_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud', 'Order', 'Order-Migration']);
              }
            }

    }
}
