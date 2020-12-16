<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Carbon\Carbon;

class OlderThanNow extends Criteria {

    private $column_name;
    private $timezone;

    function __construct($column_name, $tz = "UTC")
    {
        $this->column_name  = $column_name;
        $this->timezone = $tz;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where($this->column_name, "<=", Carbon::now($this->timezone)->toDateTimeString() );

        return $model;
    }
}