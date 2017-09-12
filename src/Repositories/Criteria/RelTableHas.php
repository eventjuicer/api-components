<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

use Context;

class RelTableHas extends Criteria {


    protected $related;
    protected $column;
    protected $value;
    protected $strict;

    function __construct($related, $column, $value = "", $strict = false)
    {
        $this->related = $related;
        $this->column = $column;
        $this->value = $value;
        $this->strict = $strict;
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
             $comparison = $this->strict ? "=" : "like";

             $query->where($this->column, $comparison, $this->value);
        });
        return $model;
    }
}