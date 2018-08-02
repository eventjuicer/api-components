<?php


namespace Eventjuicer\Services\Cascaded;

use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Eventjuicer\Models\Text as EloquentText;
use Eventjuicer\Models\Event;


class Text
{

 
    function __construct( ){
    
    }

    public function cascaded($eventId, $backend = true){

        $event = Event::findOrFail($eventId);

        $organizer = $this->transform(
            EloquentText::where("organizer_id", $event->organizer_id)->get(),
            $backend
        );

        $group = $this->transform(
            EloquentText::where("group_id", $event->group_id)->get(),
            $backend
        );

        $event = $this->transform(
            EloquentText::where("event_id", $eventId)->get(),
            $backend
        );

       return array_replace_recursive( $organizer, $group, $event );

    }


    protected function transform(Collection $collection, $backend = true)
    {


        return $collection->groupBy("name")->transform(function($name) use ($backend) {

            return $backend ? $name->keyBy("lang") : $name->mapWithKeys(function($lang){
                return [$lang["lang"] => $lang["data"]];
            }); 
        })->toArray();
    }


}