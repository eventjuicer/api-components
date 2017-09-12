<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Ticket;


class TicketTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'event'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Ticket $ticket)
    {
        return [
     
            "id" => (int) $ticket->id,       
            "organizer_id" => $ticket->organizer_id,
            "group_id" => $ticket->group_id,
            "event_id" => $ticket->event_id,
            "name" => $this->getTicketName($ticket->names),
            "date_starts" => $ticket->start,
            "date_ends" => $ticket->end,
            "offered" => $ticket->limit,
            "role" => "TO BE IMPLEMENTED"
         
        ];
    }


    public function includeEvent(Ticket $ticket)
    {
        return $this->item($ticket->event, new EventTransformer);
    }


    protected function getTicketName($str)
    {

        $data = json_decode($str, true);

        if(!empty($data) && isset($data["pl"]))
        {
            return $data["pl"];
        }
        return $data[0];
    }
}
