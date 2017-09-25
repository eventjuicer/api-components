<?php

namespace Eventjuicer\Services\Context\Facades;

use Illuminate\Support\Facades\Facade;

class Context extends Facade 
{
    protected static function getFacadeAccessor() { return 'App\Contracts\Context'; } 
    // most likely you want MyClass here
}