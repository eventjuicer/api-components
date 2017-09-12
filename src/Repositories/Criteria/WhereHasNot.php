<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class WhereHasNot extends Criteria {

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

        return $model->whereDoesntHave($this->relation, function($query)
        {
        
            foreach($this->conditions as $column => $value)
            {
                if(!is_numeric($value))
                {
                    $query->where($column, "like", $value);
                }
                else
                {
                    $query->where($column, $value);
                }

                
            }
        
        }); 

    }
}