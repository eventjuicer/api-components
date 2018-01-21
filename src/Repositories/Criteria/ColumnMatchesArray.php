<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class ColumnMatchesArray extends Criteria {

    private $column_name;
    private $value;
  

    function __construct($column_name, $value)
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

        if($this->or)
        {
             return $model->orwhere($this->column_name, "like", $this->value);
        }

        return $model->wherein($this->column_name, "like", $this->value);

    }
}