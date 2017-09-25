<?php 

namespace Eventjuicer\Services;

abstract class Able
{
	
	protected function _getRecipient(array $params, $model = null)
    {

        if(is_object($model) && $model instanceof \Illuminate\Database\Eloquent\Model )
        {
            return $model;
        }

        if(!empty($params["attach_to_model"]) && !empty($params["attach_to_model_instance_id"]))
        {

            $model = call_user_func(array("\Eventjuicer\\" . ucfirst($params["attach_to_model"]), "findOrFail"), $params["attach_to_model_instance_id"] );

            if($model->organizer_id != \Context::level()->get("organizer_id"))
            {
                throw new \Exception("Cannot attach comment to this model instance... organizer_id mismatch!");
            }

            return $model;

        }
        else
        {
             //we will attach it to the organizer only...
             return \Context::level()->get_organizer();
        }
    }

    protected function _mergeParams($params)
    {
        return is_array($params) ? array_merge($this->request->all(), $params) : $this->request->all();
    }




}