<?php

namespace Repositories;

use Models\Event;

use Carbon\Carbon;

use Cache;

use Contracts\Context;


class OrganizerEvents
{
    
    private $organizer_id;

    private $cacheExpires;

    protected $context;

    function __construct(Context $context)
    {
        $this->context = $context;

        $this->organizer_id = $this->context->level()->get("organizer_id");

        $this->cacheExpires = Carbon::now()->addMinutes(10);
    }



    public function active()
    {


        return Event::with("group")->where("organizer_id", $this->organizer_id)->get();

    	$value = Cache::remember('key', $this->cacheExpires, function()
    	{
    		return Event::with("group")->where("organizer_id", $this->organizer_id)->get();
		});


    	//$john = Cache::tags(['people', 'artists'])->get('John');

    }






}