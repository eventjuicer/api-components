<?php

namespace Eventjuicer\Crud\CompanyPurchases;

use Eventjuicer\Models\Purchase;


class FilterSkipNonCrucialTickets {

    public function filter(Purchase $item){ 
        
        $role = $item->ticketpivot->pluck("ticket.role");
        
        if( $role->contains("representative") ){
            return false;
        }
        if( $role->contains("party") ){
            return false;
        }
        return true;


    }

}