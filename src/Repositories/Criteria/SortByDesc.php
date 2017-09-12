<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


use Context;

class SortByDesc extends Criteria {

    protected $orderby;

    function __construct($orderby = "")
    {
        $this->orderby = $orderby;
    }
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->orderby($this->orderby, "DESC");
        return $model;
    }
}