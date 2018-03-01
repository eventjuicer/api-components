<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class WidgetSubtype extends Criteria {

    private $subtype;
 

    function __construct(string $subtype)
    {
        $this->subtype  = $subtype;
      
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {

        $model->where("type", "widget");

        return $model->where("subtype", "like", $this->subtype);

    }
}