<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Organizer;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

class OrganizerRepository extends Repository
{
    

    public function model()
    {
        return Organizer::class;
    }



    private $organizer_id;

    private $cacheExpires;

    protected $context;

    // function __construct()
    // {
    //     $this->cacheExpires = Carbon::now()->addMinutes(10);
    // }



  //   public function active()
  //   {


  //       return Event::with("group")->where("organizer_id", $this->organizer_id)->get();

  //   	$value = Cache::remember('key', $this->cacheExpires, function()
  //   	{
  //   		return Event::with("group")->where("organizer_id", $this->organizer_id)->get();
		// });


  //   	//$john = Cache::tags(['people', 'artists'])->get('John');

  //   }






}