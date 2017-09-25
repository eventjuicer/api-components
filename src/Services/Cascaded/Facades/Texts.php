<?php

namespace Eventjuicer\Services\Cascaded\Facades;

use Illuminate\Support\Facades\Facade;

class Texts extends Facade 
{
    protected static function getFacadeAccessor() { return 'Contracts\Text'; } 
    // most likely you want MyClass here
}