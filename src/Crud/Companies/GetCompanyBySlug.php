<?php

namespace Eventjuicer\Crud\Companies;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Company;
use Eventjuicer\Models\Group;
use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\SortBy;
use Eventjuicer\Crud\Traits\UseRouteInfo;


class GetCompanyBySlug extends Crud  {

   

    private $repo;
    private $request;

    private $sortable = [
     
    ];
    
    function __construct(CompanyRepository $repo){
        $this->repo = $repo;
    }

    public function get(){

        $this->repo->pushCriteria( new BelongsToCompany( 


        // if( $company_id > 0){

        //    $company_id ) );
        
        // }else if( $group_id > 0) {
            
        //     //we should append currently selected event_id and get group_id or organizer_id
        //     $this->repo->pushCriteria( new BelongsToOrganizer( Group::findOrFail($group_id)->organizer_id ) );
        // }

        // foreach($params as $param_name => $param_value){

        //     if( stristr($param_name, "is_") !== false ){
        //         $this->repo->pushCriteria( new FlagEquals($param_name, (int) $param_value) );
        //     }

        // }

        // $this->repo->pushCriteria( new Limit( $end, $start ) );        
        // $this->repo->pushCriteria( new SortBy($sort, $order, $this->sortable));
        
        // /**
        // * START: REVISE NEEDED
        // * we should parse images from contents!
        // */
        // $this->repo->with(["meta", "company.data", "images"]);

        // /**
        // * END: REVISE NEEDED
        // */

        // return $this->repo->all();


    }


    public function show($id){

        $this->repo->with(["participants.ticketpivot.ticket", "data"]);

        if(is_numeric($id)){
            return $this->repo->find($id);
        }

        $host = $this->getContextFromHost();

        if(!$host){
            throw new \Exception;
        }

        $this->repo->pushCriteria(new BelongsToGroup($host->getGroupId()));
        
        return $this->repo->findBy("slug", $id);

    }


    

}


