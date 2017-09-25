<?php

namespace Eventjuicer\Services\Activity;


trait ActivityTrait {
	

	function bootActivityTrait()
	{

		 protected static function registerCreatedListener()
    {
        static::created('Sofa\Revisionable\Listener@onCreated');
    }

    /**
     * Register listener for updated event.
     *
     * @return void
     */
    protected static function registerUpdatedListener()
    {
        static::updated('Sofa\Revisionable\Listener@onUpdated');
    }

    /**
     * Register listener for deleted event.
     *
     * @return void
     */
    protected static function registerDeletedListener()
    {
        static::deleted('Sofa\Revisionable\Listener@onDeleted');
    


	}	


}


