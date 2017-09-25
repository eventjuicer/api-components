<?php

namespace Eventjuicer\Services\View\Facades;

use Illuminate\Support\Facades\Facade;

class Parse extends Facade 
{
    protected static function getFacadeAccessor() { return 'Contracts\View\Dispatch'; } 

}