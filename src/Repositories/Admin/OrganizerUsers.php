<?php


namespace Repositories\Admin;

use Eventjuicer\User;

use Cache;

use Carbon;


class OrganizerUsers 
{

	function __construct()
	{

	}

	function dumb()
	{
		return new User();
	}


	function byId($id)
	{
		return User::find($id);
	}


	function get()
	{
		return User::where(["organizer_id" => \Context::level()->get("organizer_id")])->get();
	}

	function create(array $data)
	{

		$user = new User();

		$user->fill($data);

		$user->organizer_id = \Context::level()->get("organizer_id");

		$user->save(); //returns true :)

		return $this->byId( $user->id );

	}


	function update($id = 0, array $data, $replacements = array() )
	{
		$model = $this->byId($id);

		if(!empty($replacements))
		{
			foreach($replacements AS $new_key => $column)
			{
				if(!empty($data[$new_key]))
				{
					$data[$column] = $data[$new_key];
				}
			}
		}

		$model->update($data);

		return $model;
	}

}