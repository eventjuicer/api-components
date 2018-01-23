<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class ColumnMatches extends Criteria {

    private $column_name;
    private $value;
    private $or;

    function __construct(string $column_name, string $value, $or = false)
    {
        $this->column_name  = $column_name;
        $this->value        = $value;
        $this->or           = $or;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if($this->or)
        {
             return $model->orwhere($this->column_name, "like", $this->value);
        }

        return $model->where($this->column_name, "like", $this->value);

    }
}