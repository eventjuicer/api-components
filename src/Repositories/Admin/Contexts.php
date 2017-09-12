<?php

namespace Eventjuicer\Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;

use Eventjuicer\Context as Model;


class Contexts extends Repository
{

	
 	public function model()
    {
        return Model::class;
    }



    public function save(array $data)
    {

    	if(empty($data))
    	{
    		return false;
    	}

   		$slug = str_slug( $data["slug"] );

    	$model = $this->model->firstOrNew(array(
    		"organizer_id" => $this->context->get("organizer_id"),
    		"slug" => $slug
    	));

    	$model->slug = $slug;
    	$model->organizer_id = $this->context->get("organizer_id");
    	$model->description = $data["description"];


    	$model->save();

    	return $this->model;
    }

}