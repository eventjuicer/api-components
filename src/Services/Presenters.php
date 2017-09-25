<?php

namespace Eventjuicer\Services;

use Eventjuicer\Repositories\InputRepository;
use Illuminate\Support\Collection;

class Presenters {
	

	protected $presenterFields = ["fname", "lname", "avatar", "presenter", "presentation_time", "presentation_title", "presentation_description", "presentation_venue", "asd"];

	function __construct( )
	{
	}

	public function addPresenterFields(array $data)
	{
		$this->presenterFields = array_merge($this->presenterFields, $data);
	}


	public function prepare($data)
	{
		return $data->transform(function($item, $key)
		{

			$presenter = $item->fields->filter(function($_item){

				return in_array($_item->name, $this->presenterFields);

			})->mapWithKeys(function($_item){
				
				return [$_item->name => $_item->pivot->field_value];
			});

			$presenter->when(! $presenter->has("presentation_day"), function($coll){

				return $coll->put("presentation_day", "DAY1");
			});

			return $presenter->put("participant_id", $item->id);

		});
	}

	function videos(Collection $data)
	{
		$this->addPresenterFields(["video"]);

		return $this->prepare($data);
	}

	function makeByVenue(Collection $data)
	{

		return $this->prepare($data)->groupBy("presentation_day")->transform(function($item)
		{
			return $item->groupBy("presentation_venue");

		})->toArray();

	}

	function makeByTime(Collection $data)
	{

		return $this->prepare($data)->groupBy("presentation_day")->transform(function($item)
		{
			return $item->groupBy("presentation_time");

		})->toArray();

	}
}