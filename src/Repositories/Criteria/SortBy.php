<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Exception;

class SortBy extends Criteria {

    protected $sort, $order, $allowed_sorts;

    function __construct($sort = "", $order = "DESC", $allowed_sorts = [])
    {
        $this->sort = $sort;
        $this->order = $order;
        $this->allowed_sorts = $allowed_sorts;
    }
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if( !empty($this->allowed_sorts) && is_array($this->allowed_sorts) ) {

            if( in_array($this->sort, $this->allowed_sorts)){

                return $model->orderby($this->sort, $this->order);

            }else{

                throw new Exception("Bad SortByDesc Criteria Value", 1);
            }
        }

        return $model->orderby($this->sort, $this->order);
    }
}