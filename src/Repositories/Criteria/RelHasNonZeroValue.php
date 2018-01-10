<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class RelHasNonZeroValue extends Criteria {


    protected $related;
    protected $column;
    
    function __construct($related, $column)
    {
        $this->related = $related;
        $this->column = $column;
      
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
             $query->where($this->column, ">", 0);
        });

        return $model;
    }
}