<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Event;

class EventTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'group','organizer'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Event $event)
    {
        return [
            "id" => (int) $event->id,
            "group_id" => $event->group_id,
            "organizer_id" => $event->organizer_id,
            "name" => $event->names,
            "date_starts" => $event->starts,
            "date_ends" => $event->ends,
        ];
    }

    public function includeGroup(Event $event)
    {
        return $this->item($event->group, new GroupTransformer);
    }

    public function includeOrganizer(Event $event)
    {
        return $this->item($event->organizer, new OrganizerTransformer);
    }
}
