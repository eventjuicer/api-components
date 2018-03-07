<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class RelMeetsConditionsArr extends Criteria {


    protected $related;
    protected $column;
    
    function __construct($related, array $conditions)
    {
        $this->related = $related;
        $this->conditions = $conditions;
      
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->whereHas($this->related, function($query)
        {
            foreach($this->conditions as $condition)
            {

                if(isset($condition[2]))
                {
                    $query->where($condition[0], $condition[1], $condition[2]);
                }
                else {
                   $query->where($condition[0], $condition[1]);
                }
            } 
            
        });

        return $model;
    }
}