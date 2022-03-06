<?php

namespace Eventjuicer\Crud\CompanyData;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\CompanyData;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Events\ImageUrlWasProvided;
use Eventjuicer\Services\CompanyData as CompanyDataPopulate;

class Update extends Crud  {

    //use UseRouteInfo;

    protected $repo;
    
    function __construct(CompanyDataRepository $repo){
        $this->repo = $repo;

        (new CompanyDataPopulate($this->repo))->make( $this->getCompany() );

    }
    
    // public function create(){
    //     $data = $this->getData();
    //     $this->repo->saveModel($data);
    //     return $this->find( $this->repo->getId() );
    // }
    
    public function update($id){
 
        $companydata = $this->find($id);
        $name = $companydata->name;

        //value may be an array!!!
        $value = $this->getParam("value", "");
       
        // $data["group_id"] = (int) $this->getParam("x-group_id", 0);
        // $data["company_id"] = (int) $this->getParam("x-company_id", 0);
        // $data["organizer_id"] = (int) $this->getParam("x-organizer_id", 0);

        $companydata->value = $value;
        $companydata->save();

        /**
         * FRESH
         */
        $companydata->fresh();

        if($name === "logotype" || $name === "opengraph_image" ){
            event( new ImageUrlWasProvided(  $companydata ));
        }

        return $companydata;
       
    }




}





