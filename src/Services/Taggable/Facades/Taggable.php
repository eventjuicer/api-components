<?php


namespace Eventjuicer\Services\Taggable\Facades;

use Illuminate\Support\Facades\Facade;

class Taggable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'eventjuicer_taggable';
    }
}