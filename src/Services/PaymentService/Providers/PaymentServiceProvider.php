<?php

namespace App\Services\PaymentService\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymentService\Contracts\PaymentServiceContract;
use App\Services\PaymentService\PaymentService;

/**
 * Class PaymentServiceProvider
 * @package App\Providers
 */
class PaymentServiceProvider extends ServiceProvider
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
            PaymentServiceContract::class,
            PaymentService::class
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
//                  __DIR__ . '/../../config/payment.php' => "/../" . config_path('payment.php'),
//              ], ['ArtisanCloud', 'Payment', 'Payment-Config']);

              // publish migration file
//              $this->publishes([
//                  __DIR__ . '/../../config/payment.php' => "/../" . config_path('payment.php'),
//              ], ['ArtisanCloud', 'Payment', 'Payment-Model']);

              // register artisan command
              if (! class_exists('CreatePaymentTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_payments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_payments_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud', 'Payment', 'Payment-Migration']);
              }
            }

    }
}
