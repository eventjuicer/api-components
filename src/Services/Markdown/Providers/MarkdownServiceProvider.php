<?php


namespace Eventjuicer\Services\Markdown\Providers;

use Illuminate\Support\ServiceProvider;

class MarkdownServiceProvider extends ServiceProvider
{

    protected $defer = false;

    public function boot()
    {

    }

    public function register()
    {
       
    
        $this->app->singleton('Contracts\Markdown', function($app)
        {
            return new \Michelf\Markdown();
        });

       

    }

    public function provides()
    {
        return ['Contracts\Markdown'];
    }




}