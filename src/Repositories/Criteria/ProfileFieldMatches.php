<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Eventjuicer\Services\Traits\Fields;

class ProfileFieldMatches extends Criteria {


    use Fields;
    
    private $conditions;
    private $and_or_or;

    function __construct($conditions = [], $and_or_or = "")
    {
        $this->conditions   = $conditions;
        $this->and_or_or  = $and_or_or;

    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $i = 0;

        foreach($this->conditions AS $field_id => $field_value){

            if(!is_numeric($field_id)){
                $field_id = $this->getFieldId($field_id);

            }

            if($i > 0 && strtoupper($this->and_or_or) === "OR"){
                $model->orwhere(function($query) use ($field_id, $field_value) {
                    $query->where("field_id", $field_id);
                    $query->where("field_value", "like", $field_value);
                });
            }else{
                $model->where(function($query) use ($field_id, $field_value) {
                    $query->where("field_id", $field_id);
                    $query->where("field_value", "like", $field_value);
                });
            }
          
            $i++;
        }

        return $model;
    }
}