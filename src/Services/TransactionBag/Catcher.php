<?php

namespace Eventjuicer\Services\TransactionBag;


use Illuminate\Database\Eloquent\Model as Eloquent;
use Contracts\Context;

class Catcher {
	
	protected $model;
	protected $context;

	function __construct(Eloquent $model, Context $context)
	{
		$this->model 	= $model;
		$this->context 	= $context;
	}


	function add(array $ids)
	{
		// $t = new ($this->model);
		
		// $t->user_id = $this->context->user()->id();

		// $t->save();

		// return $t->id;
	}

}