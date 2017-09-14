<?php 

namespace Eventjuicer\Contracts;


use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;
use Illuminate\Support\MessageBag;


interface Importer {

	function __construct(Request $request, Config $config, MessageBag $errors);


	public function items();

	function __toString();

}