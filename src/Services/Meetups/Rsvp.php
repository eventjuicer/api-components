<?php

namespace Eventjuicer\Services\Meetups;

use Eventjuicer\Models\Meetup;


class Rsvp {
	
	protected $meetup;
	
	function __construct(Meetup $meetup)
	{
		$this->meetup = $meetup;
	}


    public function generateHash()
    {

        return sha1($this->meetup->id . 
        	"@6D2JD6yswZWA@" . 
        	strtotime($this->meetup->created_at));

    }


    public function confirm()
    {


    }


    public function reject()
    {

    	
    }


}