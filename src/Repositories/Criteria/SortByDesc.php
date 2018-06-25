<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Exception;

class SortByDesc extends Criteria {

    protected $orderby, $allowed_vars;

    function __construct($orderby = "", $allowed_vars = [])
    {
        $this->orderby = $orderby;
        $this->allowed_vars = $allowed_vars;
    }
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if( is_array($this->allowed_vars) && !empty($this->allowed_vars) ) {

            if( in_array($this->orderby, $this->allowed_vars))
            {
                return $model->orderby($this->orderby, "DESC");
            }
            else
            {
                throw new Exception("Bad SortByDesc Criteria Value", 1);
            }
        }

        return $model->orderby($this->orderby, "DESC");
    }
}