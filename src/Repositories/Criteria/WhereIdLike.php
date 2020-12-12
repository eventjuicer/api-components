<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class WhereIdLike extends Criteria {

    protected $ids;

    function __construct(string $ids)
    {
        $this->ids   = $ids;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        if(!empty($this->ids) && (is_numeric($this->ids) || strpos($this->ids, "|")!==false)){

            $model = $model->whereIn("id", explode("|", $this->ids) );
        }
        
        return $model;
    }
}