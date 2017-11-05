<?php

namespace Eventjuicer\Services;

use Eventjuicer\Repositories\InputRepository;
use Illuminate\Support\Collection;
use Eventjuicer\ValueObjects\EmailAddress;

use Eventjuicer\Services\Hashids;

class Visitors {
	

	protected $fields = ["fname", "lname", "cname2"];

	function __construct( )
	{
	}

	public function makeByCode(Collection $data)
	{

		$participants = $data->transform(function($participant, $key)
		{

			$trans = $participant->fields->filter(function($_item){

				return in_array($_item->name, $this->fields);

			})->mapWithKeys(function($_item){
				
				return [$_item->name => $_item->pivot->field_value];
			});

			return $trans->put("code", (new Hashids())->encode( $participant->id ) );

		

		})->keyBy("code");

		return ["data" => $participants];
		 

	}

	
}