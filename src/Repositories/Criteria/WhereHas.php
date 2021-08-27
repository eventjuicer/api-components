<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class WhereHas extends Criteria {

    private $relation;
    private $conditions;
    private $and;
    function __construct($relation, $conditions, $and = true)
    {
        $this->relation     = $relation;
        $this->conditions  = $conditions;
        $this->and = $and;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if(is_callable($this->conditions)){
            return $model->whereHas($this->relation, $this->conditions);
        }


        return $model->whereHas($this->relation, function($query){
        
            foreach($this->conditions as $column => $value)
            {

                $query->where($column, $value);
                
                // if(!is_numeric($value))
                // {
                //     $query->where($column, "like", $value);
                // }
                // else
                // {
                //     $query->where($column, $value);
                // }
            }
        
        }); 

    }
}