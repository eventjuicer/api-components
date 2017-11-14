<?php

namespace Eventjuicer\Providers;

use Illuminate\Support\ServiceProvider;


use Illuminate\Support\Facades\Response;

class LaravelApiComponents extends ServiceProvider
{


    public function boot()
    {


  


        // Using class based composers...

        // Using Closure based composers...
        //View::composer('xxxxxxxx', function ($view) {});a


         Response::macro('outputAsPlainText', function ($content, $name = "newsletter") {

            $headers = [
             'Content-type'        => 'text/plain',
             'Content-Disposition' => 'inline; filename="'.str_slug($name).'_'.date("YmdHi").'.html"',
            ];

            return response($content, 200, $headers);

        });

        Response::macro('downloadViewAsHtml', function ($content, $name = "newsletter") {

            $headers = [
             'Content-type'        => 'text/html',
             'Content-Disposition' => 'attachment; filename="'.str_slug($name).'_'.date("YmdHi").'.html"',
            ];

            return response($content, 200, $headers);

        });

         Response::macro('outputImage', function ($content, $name = "newsletter") {

            $headers = [
             'Content-type'        => 'image/png',
             'Content-Disposition' => 'inline; filename="'.str_slug($name).'_'.date("YmdHi").'.png"',
            ];

            return response($content, 200, $headers);

        });




    }




    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {



    }
}
