<?php 

namespace Eventjuicer\Services\Notifiers;


use Contracts\JobNotifier;

use Eventjuicer\Services\Notifiers\Drivers\Slack;

use Contracts\Context;
use Contracts\Setting;


class Notifier implements JobNotifier
{


	protected $drivers;
	protected $context;
	protected $settings;

	function __construct(Context $context, Setting $settings)
	{

		$this->context = $context;
		$this->settings = $settings;		

		//do we have a context???
	}


	function driver()
	{
		
	}











}