<?php

namespace Eventjuicer\Services\Context\Providers;

use Illuminate\Support\ServiceProvider;



class FixerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

//    protected $defer = true;

    public function boot()
    {
        //maybe here?

        return;
        
    
        \Eventjuicer\Post::updated(function($post){

            //fix comments

         //  $post->comments->where();

            //fix images

        });

        \Eventjuicer\Participant::updated(function($post){

            //fix comments

            //fix images

        });

        \Eventjuicer\User::updated(function($post){

            //fix comments

            //fix images

        });

        \Eventjuicer\Event::updated(function($post){

            //fix comments

            //fix images

        });


        \Eventjuicer\Group::updated(function($post){

            //fix comments

            //fix images

        });


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }


  //  public function provides()
    //{
      //  return ["context"];
    //}
}
