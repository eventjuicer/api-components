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
use Eventjuicer\Repositories\ParticipantDeliveryRepository;




use Eventjuicer\Services\SparkPost;
use Eventjuicer\Contracts\Email\Templated;


class ApiComponents extends ServiceProvider
{


    public function boot()
    {


        Collection::macro('paginate', function( $perPage, $total = null, $page = null, $pageName = 'page' ) {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage( $pageName );

        return new LengthAwarePaginator( $this->forPage( $page, $perPage ), $total ?: $this->count(), $perPage, $page, [
        'path' => LengthAwarePaginator::resolveCurrentPath(),
        'pageName' => $pageName,
        ]);
        });







    }




    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(Templated::class, SparkPost::class);


        $this->app->singleton(SparkPost::class, function($app)
        {

            return new SparkPost(

                $app["request"],

                $app->make(ParticipantDeliveryRepository::class)
            );

        });



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

       

    }
}
