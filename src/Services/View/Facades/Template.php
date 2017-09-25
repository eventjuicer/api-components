<?php

namespace Eventjuicer\Services\View\Facades;

use Illuminate\Support\Facades\Facade;

class Template extends Facade 
{
    protected static function getFacadeAccessor() { return 'Contracts\Template'; } 

}