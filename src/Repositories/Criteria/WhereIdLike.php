<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class WhereIdLike extends Criteria {

    protected $ids;
    protected $sep;

    function __construct($ids, $sep = "|")
    {
        $this->ids   = $ids;
        $this->sep   = $sep;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if(!empty($this->ids)){

            if(is_array($this->ids)){
                $model = $model->whereIn("id", $this->ids);
                return $model;
            }

            if(is_string($this->ids) && strpos($this->ids, $this->sep)!==false ){
                $model = $model->whereIn("id", explode($this->sep, $this->ids) );
                return $model;
            }
            
            if(is_numeric($this->ids)){
                $model = $model->whereIn("id", [$this->ids] );
                return $model;
            }

            
        }
        
        return $model;
    }
}