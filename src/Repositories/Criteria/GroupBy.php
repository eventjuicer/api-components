<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use DB;

class GroupBy extends Criteria {

    protected $column;
    protected $total;

    function __construct($column = "", $total=null)
    {
       $this->column = (string) $column;
       $this->total = $total;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository){

        if($this->total){
            $model = $model->groupBy($this->column)->select([$this->column, DB::raw(sprintf("count(*) as %s", $this->total))] );
        }else{
            $model = $model->groupBy($this->column);
        }

        return $model;
    }
}