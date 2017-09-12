<?php 

namespace Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class BelongsToEvent extends Criteria {

    protected $id;

    function __construct($id)
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
        $model = $model->where('event_id', $this->id);
        
        return $model;
    }
}