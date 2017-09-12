<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Group;

class GroupTransformer extends TransformerAbstract
{   


    protected $availableIncludes = [
        'events'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Group $group)
    {
        return [

            "id"                => (int) $group->id,
            "name"              => $group->name,
            "slug"              => $group->slug,
            "active_event_id"   => $group->active_event_id,
           
        ];
    }

    public function includeEvents(Group $group)
    {
         
        return $this->collection($group->events, new EventTransformer);
    }
}
