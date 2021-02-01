<?php

namespace Eventjuicer\ValueObjects;

use Carbon\Carbon;

class UTCDateTime {
	
	protected $datetime_original;
	protected $timezone;
	protected $datetime;
    protected $carbon;

	function __construct($datetime_original, $timezone = "")
	{
		$this->datetime_original 	= $datetime_original;
		$this->timezone 			= $timezone;

		if(empty($timezone))
		{
			throw new \Exception("No timezone defined!");
		}

		$this->carbon = $this->dateTimeToUTC($datetime_original, $timezone);
        $this->datetime = $this->carbon ? $this->carbon->toDateTimeString() : false;
	}



	public function dateTimeToUTC($dt = "", $tz = "")
    {
        
        if(! $dt instanceof Carbon)
        {
            if(! (int) $dt > 0)
            {
                return false;
            }

            $dt = Carbon::createFromFormat('Y-m-d H:i:s', (string) $dt, $tz);            
        }

        //check what timezone does the organizer have?

        $dt->setTimezone('UTC');
        return $dt;
    }



    function __call($what, $args)
    {
        return is_object($this->carbon) ? call_user_func_array(array($this->carbon, $what), $args) : null;
    }

    function __get($what)
    {
        return is_object($this->carbon) ? $this->carbon->{$what} : null;
    }

    public function isValid()
    {
        return $this->datetime !== false;
    }

    public function dateTimeNowUTC()
    {
         return (string) Carbon::now('UTC')->toDateTimeString();
    }

    public function compare()
    {
        if(!$this->datetime)
        {
            return false;
        }

    	if($this->datetime < $this->dateTimeNowUTC() )
    	{
    		return -1;
    	}

    	if($this->datetime > $this->dateTimeNowUTC() )
    	{
    		return 1;
    	}

    }

	function __set($name, $value)
	{

	}


	function __toString()
	{
		return (string) $this->datetime ? $this->datetime : "0000-00-00 00:00:00";
	}



}