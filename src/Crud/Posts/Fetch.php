<?php

namespace Eventjuicer\Crud\Posts;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Post;
use Eventjuicer\Models\PostMeta;
use Eventjuicer\Models\Group;
use Eventjuicer\Models\Company;
use Eventjuicer\Repositories\PostRepository;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\SortBy;

class Fetch extends Crud  {

    private $repo;
    private $request;

    private $sortable = [
        "id", 
        "company_id",
        "editor_id",
        "admin_id",
        "interactivity",
        "category",
        "created_at", 
        "updated_at",
        "published_at"
    ];
    
    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }

    public function get(){

        $company_id = $this->getParam("company_id");
        $group_id = $this->getParam("group_id");
        $order = $this->getParam("order");
        $start = $this->getParam("start", 0);
        $end = $this->getParam("end", 100);
        $sort = $this->getParam("sort", "id");
        $params = $this->getParams();


        if( $company_id > 0){

            $this->repo->pushCriteria( new BelongsToCompany( $company_id ) );
        
        }else if( $group_id > 0) {
            
            //we should append currently selected event_id and get group_id or organizer_id
            $this->repo->pushCriteria( new BelongsToOrganizer( Group::findOrFail($group_id)->organizer_id ) );
        }

        foreach($params as $param_name => $param_value){

            if( stristr($param_name, "is_") !== false ){
                $this->repo->pushCriteria( new FlagEquals($param_name, (int) $param_value) );
            }

        }

        $this->repo->pushCriteria( new Limit( $end, $start ) );        
        $this->repo->pushCriteria( new SortBy($sort, $order, $this->sortable));
        
        /**
        * START: REVISE NEEDED
        * we should parse images from contents!
        */
        $this->repo->with(["meta", "company.data", "images"]);

        /**
        * END: REVISE NEEDED
        */

        return $this->repo->all();


    }


    public function show($id){

        $res = $this->repo->find($id);
        return $res;

    }


    

}


