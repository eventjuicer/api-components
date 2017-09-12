<?php 

namespace Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class YoungerThanDateTime extends Criteria {

    private $column_name;
    private $value;
    private $regexp;

    function __construct($column_name, $value)
    {
        $this->column_name  = $column_name;

     /*   if(!preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $value))
        {
            throw new \Exception("Invalid input {$value}");
        }
*/
        $this->value        = $value;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where($this->column_name, ">", $this->value);

        return $model;
    }
}