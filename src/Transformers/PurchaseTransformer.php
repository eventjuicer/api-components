<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Purchase;

class PurchaseTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
      //  "participant"
    ];

    protected $availableIncludes = [
       "participant", "tickets"
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Purchase $purchase)
    {
        return [


                "id" => (int) $purchase->id,
                "organizer_id" => $purchase->organizer_id,
                "group_id" => $purchase->group_id,
                "event_id" => $purchase->event_id,
                "participant_id" => $purchase->participant_id,
                "created_at" => date("Y-m-d H:i:s", (int) $purchase->createdon),
                "updated_at" => $purchase->updatedon,
                "amount" => $purchase->amount,
                "status" => $purchase->status,
                "paid" => $purchase->paid,
                "discount" => $purchase->discount,
                "currency" => "TO BE IMPLEMENTED",




// discount_code_id
// delayed
// paid
// status
// status_source


        ];
    }

    public function includeParticipant(Purchase $purchase)
    {

        if(!$purchase->relationLoaded('participant'))
        {
            throw new \Exception("Use eager loading!");
        }

        return $this->item($purchase->participant, new ParticipantTransformer);
    }

    public function includeTickets(Purchase $purchase)
    {   

        if(!$purchase->relationLoaded('tickets'))
        {
            throw new \Exception("Use eager loading!");
        }

        return $this->collection($purchase->tickets, new TicketTransformer);
    }



}