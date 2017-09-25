<?php 

namespace Eventjuicer\Services\Install;

use Illuminate\Foundation\Bus\DispatchesJobs;


use Contracts\Context;
use Eventjuicer\Services\Install\Jobs\InstallDefaultContexts;

class Install {
	
	use DispatchesJobs;

	protected $checks = [

		"DefaultContexts",

	];

	protected $results = [];

	function __construct(Context $context)
	{

	}


	function check()
	{

		foreach($this->checks as $key) 
		{
			$class = __NAMESPACE__ . "\Checks\Check" . $key;

			$this->results[$key] = (string) new $class();
		}

		return array_sum($this->results);
	}

	function all()
	{		
		$this->dispatchNow(new InstallDefaultContexts());
			
	}



}