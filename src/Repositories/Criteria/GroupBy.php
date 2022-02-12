<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use DB;

class GroupBy extends Criteria {

    protected $column;
    protected $total;
    protected $minmax;

    function __construct($column = "", $total=null, $minmax=null)
    {
       $this->column = (string) $column;
       $this->total = $total;
       $this->minmax = $minmax;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository){

        if($this->total || $this->minmax){
            $model = $model->groupBy($this->column)->select(
                array_merge(
                    [$this->column], 
                    ($this->total ? [DB::raw(sprintf("count(*) as %s", $this->total))]: []),
                    ($this->minmax ? [DB::raw(sprintf("min(%s) as %s", $this->minmax, "min_".$this->minmax))]: []),
                    ($this->minmax ? [DB::raw(sprintf("max(%s) as %s", $this->minmax, "max_".$this->minmax))]: [])
                ));
        }else{
            $model = $model->groupBy($this->column);
        }

        return $model;
    }
}