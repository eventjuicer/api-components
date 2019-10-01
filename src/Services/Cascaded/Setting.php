<?php


namespace Eventjuicer\Services\Cascaded;

use Illuminate\Support\Collection;
use Eventjuicer\Models\Event;
use Exception;

class Setting
{

 
    function __construct( ){
    
    }

    public function cascaded($eventId, $namespace=""){

        if(empty($namespace)){
            throw new Exception("No namespace provided!");
        }

        $event = Event::findOrFail($eventId);

        $organizer = $this->transform(
            $event->organizer->settings
        );

        $group = $this->transform(
            $event->group->settings
        );

        $event = $this->transform(
            $event->settings
        );

       $merged = array_replace( $organizer, $group, $event );

       return array_filter($merged, function($key) use ($namespace){
            return strpos($key, $namespace)===0;
       }, ARRAY_FILTER_USE_KEY);

    }


    protected function transform(Collection $collection, $backend = false)
    {

        return $collection->mapWithKeys(function($item){
                return [$item["name"] => $item["data"]];
            })->all(); 

    }


}