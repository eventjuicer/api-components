<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class TaggedWith extends Criteria {

    private $tags;


    function __construct($tags)
    {
        $this->tags  = $tags;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if(empty($this->tags))
        {
            return $model;
        }

        $model = $model->whereHas('tags', function($query)
        {
            $query->whereIn('name', explode(",", $this->tags));
        });

        return $model;

    }
}