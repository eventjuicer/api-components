<?php

namespace Eventjuicer\Services\Meetups;

use Eventjuicer\Models\Meetup as Eloquent;

use Carbon\Carbon;


class Sendable {
	
	protected $meetup;

    protected $sendable;
	
	function __construct(Eloquent $meetup)
	{
		$this->meetup = $meetup;

    }


    public function check($interval = 16)
    {
        $now = Carbon::now("UTC");

        if($this->meetup->sent_at && $this->meetup->sent_at->diffInHours($now) < $interval )
        {
            return false;
        }

        if($this->meetup->resent_at && $this->meetup->resent_at->diffInHours($now) < $interval )
        {
            return false;
        }

        if($this->meetup->retries > 2)
        {
            return false;
        }

        return true;

    }


    public function __toString()
    {
        return (string) $this->check();
    }



}