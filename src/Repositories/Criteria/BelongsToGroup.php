<?php 

namespace Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

use Context;

class BelongsToGroup extends Criteria {

    protected $id;

    function __construct($id = 0)
    {
        $this->id = (int) $id ? (int) $id : Context::level()->get("group_id") ;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where('group_id', $this->id);
        return $model;
    }
}