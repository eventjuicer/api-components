<?php

namespace Eventjuicer\Services\View\Exceptions;


//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Exception\RuntimeException;

class PlaceholderNotFoundException extends RuntimeException 
{
	
	function __construct()
	{
		parent::__construct("Template or config error - bad placeholder for ASSETS");
	}


}