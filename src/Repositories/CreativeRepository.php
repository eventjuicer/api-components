<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Creative;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Services\ApiUser;

class CreativeRepository extends Repository
{
    

    public function model()
    {
        return Creative::class;
    }


    public function _create(array $data, ApiUser $user)
    {
    	$data = $this->prepareData($data, $user);

        $newCreative = $this->saveModel($data);

        return $this->find($this->model->id);

    }

     public function _update($id, array $data, ApiUser $user)
    {	
    	//$model = $this->find($id);

    	$data = $this->prepareData($data, $user);

        $this->update($data, $id);

        return $this->find($this->model->id);

    }


    protected function prepareData(array $data, ApiUser $user)
    {
    	$data["name"] = str_slug($data["name"]);
        $data["act_as"] = "newsletter";
        $data["organizer_id"] = $user->organizer_id;
        $data["group_id"] = $user->group_id;
        $data["company_id"] = $user->company()->id;
        $data["data"] = json_encode($data["data"]);

        return $data;
    }



}