<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Host;

class HostTransformer extends TransformerAbstract
{



    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Host $host)
    {
        return [
     
            "group_id"      => (int) $host->group_id,     
            "organizer_id"  => (int) $host->organizer_id, 
            "active_event_id" => 0,
            "api_endpoint" => "",
            "lastmod"   => [

                "pages" => 1111,
                "configs" => 2222,
                "tickets" => 3333,
                "settings" => 4444,
                "texts" => 5555

            ],

        ];
    }
   
}