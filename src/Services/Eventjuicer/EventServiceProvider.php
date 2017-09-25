<?php

namespace Eventjuicer\Services\Eventjuicer;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;



use View;

use Contracts\Context;

use Contracts\Setting;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */



    protected $listen = [

        'Events\RichContentModelUpdated' => [

            'Listeners\PreParser@handle',
            "Listeners\ImageImporter@handle",
        ],

        
       'SocialiteProviders\Manager\SocialiteWasCalled' => [
        
            'SocialiteProviders\Todoist\TodoistExtendSocialite@handle',
            'SocialiteProviders\Bitly\BitlyExtendSocialite@handle',
            'SocialiteProviders\Buffer\BufferExtendSocialite@handle'

        ],

        "Events\CachableItemWasChanged" => [

            "Listeners\CachableItemReCache@handle"
        ],

        'Illuminate\Cache\Events\KeyWritten' => [
            'Listeners\CacheCreated@handle',
        ],


        "Events\ItemsAddedToTransactionBag" => [
            "Listeners\TransactionBag@handle"
        ]

    ];


   

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {

        Event::listen('Illuminate\Routing\Events\RouteMatched', function (\Illuminate\Routing\Events\RouteMatched $route)

        {        

            \App::make("Contracts\Context")->create($route->route->parameters());

            \App::make("Contracts\Template")->create($route->route->parameters());

            \App::make("Contracts\Setting")->make();
            \App::make("Contracts\Text")->make();
            \App::make("Contracts\Page")->make();
            \App::make("Contracts\Widget")->make();

        });


        parent::boot();
    }
}
