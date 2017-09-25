<?php


namespace Eventjuicer\Services\Commentable\Facades;

use Illuminate\Support\Facades\Facade;

class Commentable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'commentable';
    }
}