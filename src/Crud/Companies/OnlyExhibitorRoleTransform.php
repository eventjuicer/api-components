<?php

namespace Eventjuicer\Crud\Companies;

use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Company;

class OnlyExhibitorRoleTransform {

    public $repo;
    
    public $allowedRoles = ["exhibitor", "presenter"];

    function __construct(CompanyRepository $repo){
        $this->repo = $repo;
    }

    public function transform(Company $item){


        $item->participants->transform(function($participant){

            $has_allowed_roles = $participant->ticketpivot->filter(function($ticketpivot){

                return in_array( $ticketpivot->ticket->role, $this->allowedRoles);
    
            });

            return $has_allowed_roles->count()? $participant : null;
            
        });
        
        return $item;
        
    }


}