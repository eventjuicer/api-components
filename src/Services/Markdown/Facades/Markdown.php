<?php

namespace Eventjuicer\Services\Markdown\Facades;

use Illuminate\Support\Facades\Facade;

class Markdown extends Facade 
{
    protected static function getFacadeAccessor() { 
    	return 'Contracts\Markdown'; 
    } 

}