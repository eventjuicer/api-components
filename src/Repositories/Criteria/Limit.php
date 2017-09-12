<?php 

namespace Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class Limit extends Criteria {

    protected $take;
    protected $skip;

    function __construct($take, $skip = 0)
    {
        $this->take = $take;
        $this->skip = $skip;
    }
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->take($this->take)->skip($this->skip);
        return $model;
    }
}