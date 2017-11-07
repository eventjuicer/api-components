<?php

namespace Eventjuicer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



use Eventjuicer\Services\ParticipantPromo;
use Eventjuicer\Services\ParticipantPromoCreatives;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CreativeRepository;
use Eventjuicer\Repositories\CreativeTemplateRepository;


class ApiComponents extends ServiceProvider
{


    public function boot()
    {

        // Using class based composers...

        // Using Closure based composers...
        //View::composer('xxxxxxxx', function ($view) {});a


         \Response::macro('outputAsPlainText', function ($content) {

            $headers = [
             'Content-type'        => 'text/plain',
             'Content-Disposition' => 'inline; filename="newsletter_'.date("YmdHi").'.html"',
            ];

            return \Response::make($content, 200, $headers);

        });

        \Response::macro('downloadViewAsHtml', function ($content) {

            $headers = [
             'Content-type'        => 'text/html',
             'Content-Disposition' => 'attachment; filename="newsletter_'.date("YmdHi").'.html"',
            ];

            return \Response::make($content, 200, $headers);

        });

         \Response::macro('outputImage', function ($content) {

            $headers = [
             'Content-type'        => 'image/png',
             'Content-Disposition' => 'inline; filename="newsletter_'.date("YmdHi").'.png"',
            ];

            return \Response::make($content, 200, $headers);

        });




    }




    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


        $this->app->singleton(ParticipantPromo::class, function($app)
        {

            return new ParticipantPromo(

                $app["request"],

                $app->make(ParticipantRepository::class),

                $app->make(CreativeRepository::class)

            );

        });

        $this->app->singleton(ParticipantPromoCreatives::class, function($app)
        {

            return new ParticipantPromoCreatives(

                $app->make(ParticipantPromo::class),
                $app->make(CreativeRepository::class),
                $app->make(CreativeTemplateRepository::class)
            );

        });

        Collection::macro('paginate', function( $perPage, $total = null, $page = null, $pageName = 'page' ) {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage( $pageName );

        return new LengthAwarePaginator( $this->forPage( $page, $perPage ), $total ?: $this->count(), $perPage, $page, [
        'path' => LengthAwarePaginator::resolveCurrentPath(),
        'pageName' => $pageName,
        ]);
        });

    }
}
