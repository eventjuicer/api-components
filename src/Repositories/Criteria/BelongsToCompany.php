<?php 

namespace Eventjuicer\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

use Eventjuicer\Models\Company;

class BelongsToCompany extends Criteria {

    protected $id;

    function __construct($id, int $organizer_id = 0){

        if( $organizer_id > 0 && !is_numeric($id) ){

            $company = Company::where("slug", "like", $id)->where("organizer_id", $organizer_id)->get();

            $this->id = $company->first()->id;

        }else{
            $this->id = (int) $id;
        }

    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $model = $model->where('company_id', $this->id);
        return $model;
    }
}