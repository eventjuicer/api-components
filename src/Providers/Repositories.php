<?php

namespace Eventjuicer\Providers;

use Illuminate\Support\ServiceProvider;


use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;

use Eventjuicer\Repositories\TicketRepositoryInterface;
use Eventjuicer\Repositories\EloquentTicketRepository;
use Eventjuicer\Repositories\ElasticsearchTicketRepository;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Eventjuicer\Artisan\ElasticsearchReindexCommand;

class Repositories extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->bindElasticsearch();
        $this->registerConsoleCommands();

        $this->app->singleton(TicketRepositoryInterface::class, function($app) {
            // This is useful in case we want to turn-off our
            // search cluster or when deploying the search
            // to a live, running application at first.
            // if (!config('services.search.enabled')) {
                return new ElasticsearchTicketRepository(  
                     $app->make(Client::class)
                );
            // }

            return new EloquentTicketRepository(

                $app->make("Illuminate\Contracts\Cache\Repository"),
                $app->make("Illuminate\Http\Request"),
                $app->make("Illuminate\Container\Container"),
                $app->make("Illuminate\Support\Collection")

            );

            // return new ElasticsearchArticlesRepository(
            //     $app->make(Client::class)
            // );
        });

    }

    private function registerConsoleCommands()
    {
        $this->commands([
            ElasticsearchReindexCommand::class
        ]);
    }


    private function bindElasticsearch()
    {
        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()
               // ->setHosts(config('services.search.hosts'))
                ->build();
        });
    }
}
