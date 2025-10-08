<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class WhereColumnInSeparatedValues extends Criteria {

    protected $column;
    protected $ids;
    protected $sep;
    function __construct(string $column, string $ids, $sep = ",")
    {
        $this->column = $column;
        $this->ids   = trim(strval($ids));
        $this->sep   = $sep;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $ids = array_filter(explode($this->sep, $this->ids));

        if(!empty($ids)){
            $model = $model->whereIn($this->column, $ids);
        }
        
        return $model;
    }
}