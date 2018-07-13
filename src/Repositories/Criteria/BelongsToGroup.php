<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class BelongsToGroup extends Criteria {

    protected $id;

    function __construct($id = 0)
    {
       $this->id = (int) $id;
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