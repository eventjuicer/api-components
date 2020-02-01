<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class ColumnMatchesArray extends Criteria {

    private $conditions;

    function __construct(array $conditions)
    {
        $this->conditions  = $conditions;
      
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        foreach($this->conditions as $cond){
            $parsed = explode(",", $cond);
            if(count($parsed)==3){
                $model->where($parsed[0], $parsed[1], $parsed[2]);
            }else{
                $model->where($parsed[0], "=", $parsed[1]);
            }
        }
        return $model;

    }
}