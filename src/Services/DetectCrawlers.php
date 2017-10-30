<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;

		
class DetectCrawlers {

	protected $ua;

	protected $test;

	protected $crawlers = [

		"facebook" => [

			'facebookexternalhit',
            'Facebot'

		],

		"twitter" => [

			'Twitterbot',
		]
            
    ];

	function __construct(Request $request)
	{

		$this->ua = $request->header('User-Agent');

		$this->test = $request->input("test", false);
	
	}


	public function any()
	{

		if($this->test)
		{
			return true;
		}

		foreach($this->crawlers as $name => $uas)
		{
			if($this->{$name}())
			{
				return true;
			}
		}

		return false;
	}



	function __call($name, $params = [])
	{

		if(!isset($this->crawlers[$name]))
		{
			throw new \Exception("Crawler unknown");
		}

		if($this->test)
		{
			return true;
		}

        foreach($this->crawlers[$name] as $c)
        {
        	if(strpos($this->ua, $c)!==false)
        	{
        		return true;
        	}
        }

        return false;
	}


}