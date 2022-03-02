<?php

namespace Eventjuicer\Crud\Companies;

use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Crud\Traits\UseRouteInfo;
use Eventjuicer\Models\Company;

class TransformOnlyActivePurchases {

    use UseRouteInfo;

    public $repo;
 

    function __construct(CompanyRepository $repo){
        $this->repo = $repo;
    }

    public function transform(Company $item){

        $context = $this->getContextFromHost();

        $active_event_id = $context->getEventId();

        $item->participants = $item->participants->filter(function($registration) use ($active_event_id) {

            $has_sold = $registration->ticketpivot->filter(function($purchase){

                return $purchase->sold;
            });

            return $has_sold->count() && $registration->event_id == $active_event_id;

        });
        
        return $item;
        
    }


}