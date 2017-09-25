<?php

//https://murze.be/2015/07/upload-large-files-to-s3-using-laravel-5/


namespace Eventjuicer\Services\Taggable\Providers;

use Illuminate\Support\ServiceProvider;

class TaggableServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('eventjuicer_taggable', function ($app)
        {
            return new \Services\Taggable\Taggable($app['request'], $app['config']['taggable']);
        });

    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(){}

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['eventjuicer_taggable'];
    }
}