<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class ColumnNotEmpty extends Criteria {

    private $column_name;

    function __construct($column_name)
    {
        $this->column_name  = $column_name;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        $model = $model->where($this->column_name, "!=", "''");

        return $model;
    }
}