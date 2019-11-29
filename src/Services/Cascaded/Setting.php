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

       return $namespace ? array_filter($merged, function($key) use ($namespace){
            return strpos($key, $namespace)===0;
       }, ARRAY_FILTER_USE_KEY) : $merged;

    }


   public function makeMultiDimensional(array $items, $delimiter = '.'){
        $new = array();
        foreach ($items as $key => $value) {
            if (strpos($key, $delimiter) === false) {
                $new[$key] = is_array($value) ? $this->makeMultiDimensional($value, $delimiter) : $value;
                continue;
            }

            $segments = explode($delimiter, $key);
            $last = &$new[$segments[0]];
            if (!is_null($last) && !is_array($last)) {
                throw new \LogicException(sprintf("The '%s' key has already been defined as being '%s'", $segments[0], gettype($last)));
            }

            foreach ($segments as $k => $segment) {
                if ($k != 0) {
                    $last = &$last[$segment];
                }
            }
            $last = is_array($value) ? $this->makeMultiDimensional($value, $delimiter) : $value;
        }
        return $new;
    }

    protected function transform(Collection $collection, $backend = false)
    {

        return $collection->mapWithKeys(function($item){
                return [$item["name"] => json_decode($item["data"], true)];
            })->all(); 

    }


}