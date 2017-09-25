<?php

namespace Eventjuicer\Services\MillenetCsv;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


use Route;
use View;

use Eventjuicer\Services\Context\ViewComposers\Shared;
use Eventjuicer\Services\Context\ViewComposers\Costapp;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    
        /* $this->loadViewsFrom(__DIR__.'/views', 'timezones');

        $this->publishes([
        __DIR__.'/views' => base_path('resources/views/laraveldaily/timezones'),
        ]);
        */

        View::composer('*', Shared::class);
        View::composer('*', Costapp::class);

        $this->loadViewsFrom(__DIR__.'/Http/views', 'millenetcsv');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton("Services\MillenetCsv\Import", function($app){

            return new Import(

                $app->make('excel')
                
                );
        });

        Route::resource('admin/imports', Http\ImportController::class);

      //  include __DIR__.'/routes.php';

      //  $this->app->make('Az\PolskieBramkiPlatnosci\PublicController');

       // $this->app->make(ExcelServiceProvider::class);
    }
}
