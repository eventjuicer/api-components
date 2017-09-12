<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class WhereIn extends Criteria {

    protected $column;
    protected $vals;

    function __construct(string $column, array $vals)
    {
        $this->column   = $column;
        $this->vals     =  $vals;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->whereIn($this->column, $this->vals);
        
        return $model;
    }
}