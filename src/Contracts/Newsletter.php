<?php

namespace Eventjuicer\Contracts;



use Contracts\Context;
use Contracts\Setting;
use ServicesRepository;

interface Newsletter {
	
	function __construct(Context $context, Setting $setting, Repository $repository);	

	public function confirm($token);

	public function subscribe($email = "");

}