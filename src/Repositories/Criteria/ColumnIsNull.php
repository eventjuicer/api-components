<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

 
class ColumnIsNull extends Criteria {

    protected $column;

    function __construct($column = "")
    {
        $this->column = $column;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->whereNull($this->column);
        return $model;
    }
}