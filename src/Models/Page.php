<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

//use Sofa\Revisionable\Laravel\Revisionable;

class Page extends Model
{

	//use Revisionable;


    protected $table = "eventjuicer_pages";

   	protected $casts = ["data"=>"array"];

   	protected $fillable = ["name", "data"];


   	protected $revisionPresenter = 'Presenters\Revisions\Page';

	protected $revisionable = [
		'data'
	];


    public function pageable()
    {
        return $this->morphTo();
    }


 	public function getPresenter()
    {
    	if(\Context::app()->in_admin())
    	{
    		 return new AdminPresenter($this);
    	}

        return new PublicPresenter($this);
    }





}
