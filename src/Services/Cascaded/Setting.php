<?php


namespace Eventjuicer\Services\Cascaded;

use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\Setting as Eloquent;
use Eventjuicer\Models\Event;


class Setting
{

 
    function __construct( ){
    
    }

    public function cascaded($eventId){

        $event = Event::findOrFail($eventId);

        $organizer = $this->transform(
            Eloquent::where("organizer_id", $event->organizer_id)->get()
        );

        $group = $this->transform(
            Eloquent::where("group_id", $event->group_id)->get()
        );

        $event = $this->transform(
            Eloquent::where("event_id", $eventId)->get()
        );

       return array_replace_recursive( $organizer, $group, $event );

    }


    protected function transform(Collection $collection, $backend = false)
    {


        return $collection->groupBy("name")->transform(function($name) use ($backend) {

            return $backend ? $name->keyBy("lang") : $name->mapWithKeys(function($lang){
                return [$lang["lang"] => $lang["data"]];
            }); 
        })->toArray();
    }


}