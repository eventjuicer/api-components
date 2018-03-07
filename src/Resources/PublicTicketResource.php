<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
 
use Eventjuicer\Services\Personalizer;

use Carbon\Carbon;

class PublicTicketResource extends Resource
{

	protected $roles = ["contestant", "visitor", "exhibitor" , "presenter"];

	protected $limitFromGroup;

	public function __construct($resource, $limitFromGroup = 0)
    {
        $this->resource = $resource;
        $this->limitFromGroup = $limitFromGroup;
    }

    public function toArray($request)
    {   
    	$datePassed 	= Carbon::now()->greaterThan( $this->end );
 		$dateInFuture 	= Carbon::now()->lessThan( $this->start );

    	$data = [];

 		$data["id"] 		= $this->id;
 		$data["group_id"] 	= $this->ticket_group_id;
 		
 		$data["names"] 		= $this->names;
 		$data["price"] 		= $this->price;
 		$data["role"] 		= in_array($this->role, $this->roles) ? $this->role : "";
 		
 		$data["limit"] 		= $this->limit;
 		$data["max_quantity"] = $this->max;

 		$data["start"] 		= (string) $this->start;
 		$data["end"] 		= (string) $this->end;

 		$data["customers"] 	= $this->ticketpivot->count();

 		$data["sold"] 		= $this->ticketpivot->sum("quantity");

 		$data["remaining"] 	= $data["limit"] - $data["sold"];

 		$data["in_dates"] 	= intval( !$datePassed && !$dateInFuture );

 		$data["bookable"] 	= intval( $data["remaining"] && $data["in_dates"] );



 		
 		$data["errors"] 	= [];

 		if(! $data["in_dates"] )
 		{
 			
 			if($datePassed)
 			{
 				$data["errors"][] = 'overdue';
 			}

 			if(!$datePassed && $dateInFuture)
 			{
 				$data["errors"][] = 'future';
 			}
  		}

  		if(! $data["remaining"] > 0 )
  		{
  			$data["errors"][] = 'soldout';
  		}

 		
 		
        return $data;
    }
}



