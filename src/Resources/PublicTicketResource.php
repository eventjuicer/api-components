<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Personalizer;

use Carbon\Carbon;

use Illuminate\Support\Collection;

class PublicTicketResource extends Resource{

    /** 
    * moved to TicketsSold 
    * // public static function enhanceWithGroupInfo(Collection $groupsByGroupId)
    * // {self::$groupInfo = $groupsByGroupId;}
    */ 

    public function toArray($request){   

    	$data = [];
 		$data["id"] 		= $this->id;
 		$data["group_id"] 	= $this->ticket_group_id;
 		$data["names"] 		= $this->names;
 		$data["price"] 		= $this->price;
 		$data["role"] 		= $this->role;
 		$data["limit"] 		= $this->limit;
 		$data["max_quantity"] = $this->max;
 		$data["start"] 		= (string) $this->start;
 		$data["end"] 		= (string) $this->end;
        $data["thumbnail"]    = (string) $this->thumbnail;
        $data["image"]    = (string) $this->image;
        $data["translation_asset_id"] = (string) $this->translation_asset_id;
        $data["details_url"] = (string) $this->details_url;

        /** 
         * moved to TicketsSold 
         *  //"customers" => $this->customers, //$this->ticketpivot->count(),
         * //"sold"      => $this->sold, //$this->ticketpivot->sum("quantity")
         **/
 		$data["agg"] =  $this->agg;
        $data["remaining"] = $this->remaining;

        /**
         * moved to TicketsSold
         * 
         * //if($this->ticket_group_id && self::$groupInfo){
         * //$group = self::$groupInfo[$this->ticket_group_id];
         * //$remainingInGroup = $group->limit - $group->agg["sold"];
         * //$data["remaining"]  = min($remainingInGroup, ($data["limit"] - $data["agg"]["sold"]) );
         * //}else{
         * //$data["remaining"]  = $data["limit"] - $data["agg"]["sold"];
         * //}
         **/

 		/** 
         * moved to TicketsSold 
         * //intval( !$datePassed && !$dateInFuture );
         **/
 		$data["in_dates"] 	= $this->in_dates; 
        
        /** 
         * moved to TicketsSold 
         * //intval( $data["remaining"] && $data["in_dates"] );
         **/
 		$data["bookable"] 	= $this->bookable;
 		
        /**
         * moved to TicketSold
         * 
         * //$data["errors"]  = [];
         * //if(! $data["in_dates"] ){
         * //if($datePassed){$data["errors"][] = 'overdue';}
         * //if(!$datePassed && $dateInFuture){$data["errors"][] = 'future';}
         * //}
         * //if(! $data["remaining"] > 0 ){
         * //$data["errors"][] = 'soldout';
         * //if(isset($remainingInGroup)){$data["errors"][] = 'soldout_pool';}
         * //}
         */

        $data["errors"] = $this->errors;

        return $data;
    }
}



