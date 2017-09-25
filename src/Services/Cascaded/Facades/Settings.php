<?php

namespace Eventjuicer\Services\Cascaded\Facades;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade 
{
    protected static function getFacadeAccessor() { return 'Contracts\Setting'; } 
    // most likely you want MyClass here
}