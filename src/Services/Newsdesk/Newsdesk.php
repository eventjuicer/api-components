<?php

namespace Eventjuicer\Services\Newsdesk;

use Contracts\Context;

use Contracts\SearchApi;

use Bosnadev\Repositories\Contracts\RepositoryInterface;


//https://github.com/scotteh/php-goose


class Newsdesk implements SearchApi {
	
	protected $sources;
	protected $items;
	protected $context;

	function __construct(Context $context, RepositoryInterface $sources, RepositoryInterface $items)
	{

		$this->sources 	= $sources;
		$this->items 	= $items;
		$this->context 	= $context;

	}


	function run()
	{
		//check what should be parsed


		dd($this->sources->all()->toArray());
	}


}