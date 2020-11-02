<?php

namespace App\Services\QuotationService\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\QuotationService\Contracts\QuotationServiceContract;
use App\Services\QuotationService\QuotationService;

/**
 * Class QuotationServiceProvider
 * @package App\Providers
 */
class QuotationServiceProvider extends ServiceProvider
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
            QuotationServiceContract::class,
            QuotationService::class
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
//                  __DIR__ . '/../../config/quotation.php' => "/../" . config_path('quotation.php'),
//              ], ['ArtisanCloud', 'Quotation', 'Quotation-Config']);

              // publish migration file
//              $this->publishes([
//                  __DIR__ . '/../../config/quotation.php' => "/../" . config_path('quotation.php'),
//              ], ['ArtisanCloud', 'Quotation', 'Quotation-Model']);

              // register artisan command
              if (! class_exists('CreateQuotationTable')) {
                $this->publishes([
                  __DIR__ . '/../../database/migrations/create_quotations_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_quotations_table.php'),
                  // you can add any number of migrations here
                ], ['ArtisanCloud', 'Quotation', 'Quotation-Migration']);
              }
            }

    }
}
