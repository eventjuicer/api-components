<?php 

namespace Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;


use Services\Repository;


use Eventjuicer\CostTemplate;





class OrganizerDocumentTemplates extends Repository
{

	public function model()
	{
        return CostTemplate::class;
    }



    public function create(array $data)
    {

    	$model = new CostTemplate;

    	$model->organizer_id = $this->context->get("organizer_id");

        $model->fill($data)->save();

        return $model;

    }

	public function update(array $data, $id)
	{
    	//return true;

    	$model = $this->find($id);

    	$model->organizer_id = $this->context->get("organizer_id");

        return $model->fill($data)->save();
    
    }


}