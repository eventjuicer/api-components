<?php

namespace Eventjuicer\Resources\ExtApi;
use Illuminate\Http\Resources\Json\Resource; 


class CompanyResource extends Resource {


    public static $skipPurchases = false;

    public static function disablePurchases(){
        self::$skipPurchases = true;
    }


    public function toArray($request){   

        $purchases = $this->participants->pluck("ticketpivot")->collapse()->where("sold", 1)->map(function($purchase) {
            $purchase->setHidden(['ticket']);
            return array_merge(
                $purchase->toArray(),
                $purchase->relationLoaded('ticket') && $purchase->ticket 
                    ? ['role' => $purchase->ticket->role ?? null]
                    : []
            );
        });

        $purchasesGrouped = $this->participants
        ->flatMap(function($participant) {
            return $participant->purchases->where('status', '!=', 'cancelled');
        })
        ->groupBy('event_id')
        ->map(function($eventPurchases) {
            return [
                'total_amount' => $eventPurchases->sum('amount'),
                'total_discount' => $eventPurchases->sum('discount')
            ];
        });

        $groupedPurchases = $purchases->groupBy("event_id");

        $profile = $this->data->mapWithKeys(function($item){     
                return [ $item->name => $item->value ];
        })->all();

        $lang = !empty($profile["lang"]) ? $profile["lang"] : ($this->group_id > 1 ? "en" : "pl");

        $data = [

            "id" => $this->id,     
            
            "group_id" =>(int) $this->group_id,
            "organizer_id" => (int) $this->organizer_id,
            "created_at" => (string) $this->created_at,
            "updated_at" => (string) $this->updated_at,

            "lang" => $lang,

            "slug" => $this->slug,

            "profile" => $profile,

            "event_ids" => $purchases->pluck("event_id")->unique()->values(),

          	"instances" => $groupedPurchases,

            "people" => $this->whenLoaded("people") ? $this->people: [],

            "purchases" => $purchasesGrouped,
            
        ];
    
        if(empty($data["profile"]["name"]))
        {
            $data["profile"]["name"] = $this->slug;
        }
     


        return $data;
    }
}



