<?php

namespace Eventjuicer\Services\Eventjuicer;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;



use Blade;
use Queue;



use Eventjuicer\Services\Cascaded\Cascaded;
use Eventjuicer\Services\Cascaded\Drivers\Texts;

use Eventjuicer\Models\User;
use Eventjuicer\Models\Text;
use Eventjuicer\Models\Setting;
use Eventjuicer\Models\Page;

use Eventjuicer\Services\View\Assets;
use Eventjuicer\Services\View\Template as MyTemplate;
use Eventjuicer\Services\View\ParserHelper;
use Contracts\Template;




class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {



        Route::pattern('year',        '[0-9]{4}');
        Route::pattern('month',       '[0-9]{2}');
        Route::pattern('slug',        '[a-z0-9\-]+');
        Route::pattern('token',       '[a-z0-9]{32,}');
        Route::pattern('keyname',     '[a-z0-9\.]+');
        Route::pattern('subpage',     '[a-z0-9\-]+');
        Route::pattern('account',     '^(?!(api|static|links|files|cdn|www|admin))([a-z0-9\-]{3,})$');

        Route::pattern('project',     '^[a-z0-9\.\-]{3,}$'); //checked

        Route::pattern('host',        '^((?!.*(costapp|eventjuicer|editorapp))[a-z0-9\.\-]{3,})$'); //checked! negative look around

        Route::pattern('event_id',      '[0-9]+');
        Route::pattern('post_id',       '[0-9]+');
        Route::pattern('user_id',       '[0-9]+');
        Route::pattern('participant_id','[0-9]+');
        Route::pattern('page_id',       '[0-9]{1,3}');
        Route::pattern("provider",      '[a-z]+');
        



        $this->mergeConfigFrom(
            __DIR__ ."/config/app.php", 'app'  
        );

        $this->loadRoutesFrom(
            __DIR__.'/routes/api.php'
        );





        // Route::prefix('api')
        //      ->middleware('api')
        //      ->namespace("Controllers")
        //      ->group(base_path('routes/routes.eventjuicer.php'));


/*
        Queue::failing(function(JobFailed $event) use ($notify)
        {
            // 
            // 
            // $event->data

             $notify->from('system')->to('@adam')->send("Job failing! {$event->job} {$event->connectionName} ");

        });
    
*/

        if(\App::environment('production'))
        {
             $this->app['request']->server->set('HTTPS', true);
        }
       

        /*
        Queue::after(function (JobProcessed $event)
        {
            //$command = unserialize($event->data['data']['command']);
        });

        */

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {   


      



        $this->app->bind("Contracts\View\Parser", 
            "Services\View\Parser"
        );
  
        $this->app->bind("Contracts\View\Dispatch", "Services\View\Dispatch");


        $this->app->singleton("Contracts\Newsletter", function($app)
        {   

           return new \Services\Newsletter\Newsletter(
                $app["Contracts\Context"], 
                $app["Contracts\Setting"],
                $app["Repositories\Admin\Participants"]); 

        });


     


     
     

        $this->app->singleton("Contracts\SearchApi", function($app){


           return new \Services\Newsdesk\Newsdesk(

                $app["Contracts\Context"],

                $app["Repositories\Admin\NewsdeskSources"],

                $app["Repositories\Admin\NewsdeskItems"]

                );

        });

        $this->app->singleton("Services\TransactionBag\Catcher", function($app){

            return new \Services\TransactionBag\Catcher(

               $app["Transaction"],
               $app["Contracts\Context"]

            );

        });


        $this->app->bind('Contracts\Commentable',   'Services\Commentable\Commentable');
        $this->app->bind('Contracts\Taggable',      'Services\Taggable\Taggable');
        $this->app->bind('Contracts\Imageable',     'Services\ImageHandler\ImageHandler');
        

        $this->app->singleton('Contracts\Importer',  function($app)
        {
            return new \Services\ImageHandler\Importer();
        });   

        $this->app->singleton('Contracts\Context', function($app)
        {
            $context = new \Services\Context\Context( $app["request"], $app["config"]["context"] );

            $context->register("level", "Services\Context\Level");
            $context->register("app",   "Services\Context\App");
            $context->register("user",  "Services\Context\User");

            $context->registerApp("*",    "Services\Context\ViewComposers\Shared", ['admin/*','errors/*'] );
          
            $context->registerApp("editorapp",   "Services\Context\ViewComposers\Editorapp",  ['admin/*','errors/*'] );
            $context->registerApp("eventjuicer", "Services\Context\ViewComposers\Eventjuicer", ['admin/*','errors/*']);
            $context->registerApp("salesjuicer", "Services\Context\ViewComposers\Eventjuicer", ['admin/*','errors/*']);
            $context->registerApp("costapp",     "Services\Context\ViewComposers\Costapp",     ['admin/*','errors/*']);

            return $context;

        });


        $this->app->singleton('Contracts\Setting', "Services\Cascaded\Drivers\Settings");

        $this->app->singleton('Contracts\Text', "Services\Cascaded\Drivers\Texts");

        $this->app->singleton('Contracts\Page', "Services\Cascaded\Drivers\Pages");
       
        $this->app->singleton('Contracts\Widget', "Services\Cascaded\Drivers\Pages");







        $this->app->singleton('Services\View\Assets', function($app)
        {

            return new Assets($app["request"], $app["config"]["assets"], $app["Contracts\Context"]);
        });


        $this->app->singleton('Contracts\Template', function($app)
        {        
            return new MyTemplate($app["request"], $app["config"], $app["Contracts\Context"]);
        });


     


        /*    $this->app->singleton('TeamUserInvitator', function($app)
            {
                return new \Services\UserInvitator\UserInvitator();
            });

            $this->app->bind("Contracts\UserInvitator", "TeamUserInvitator");
        */

    }
}
