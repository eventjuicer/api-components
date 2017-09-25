<?php

namespace Eventjuicer\Services\Sender;



use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;
use Illuminate\Support\MessageBag;

use Contracts\Importer;



//adapters
use Eventjuicer\Services\Sender\File;
use Eventjuicer\Services\Sender\Textarea;

class Import implements Importer {



	protected $request, $config, $errors;


	protected $storage, $upload_path;

	protected $organizer_id, $user_id;

	
	protected $results = [];

	protected $description = "";


	protected $adapters = [];

	function __construct(Request $request, Config $config, MessageBag $errors)
	{
		$this->request = $request;
		$this->config = $config;
		$this->errors = $errors;
	}

	function file($forminput)
	{
		$this->adapters[] = new File($this->request, $this->config, $this->errors, $forminput);
	}

	function textarea($forminput)
	{
		$this->adapters[] = new Textarea($this->request, $this->config, $this->errors, $forminput);
	}

	function items()
	{
		$items = [];

		foreach($this->adapters AS $adapter)
		{
			$items = $items + $adapter->items();
		}

		return $items;
	}

	function __toString()
	{

	}

}