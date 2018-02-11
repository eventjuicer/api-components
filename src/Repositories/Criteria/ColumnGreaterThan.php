<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class ColumnGreaterThan extends Criteria {

    protected $column, $value;

    function __construct(string $column, $value)
    {
        $this->column = $column;
        $this->value = $value;

    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where($this->column, ">", $this->value);
        return $model;
    }
}