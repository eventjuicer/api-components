<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class FlagEquals extends Criteria {

    private $column_name;
    private $value;
    private $regexp;

    function __construct($column_name, $value = 1)
    {
        $this->column_name  = $column_name;
        $this->value        = $value;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        foreach($repository->getCriteria() AS $criteria)
        {
            if($criteria instanceOf self)
            {
               //merge conditions? :)
            }
        }

        $model = $model->where($this->column_name, $this->value);

        return $model;
    }
}