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

        foreach($item->participants AS $participant){

            $participant->ticketpivot = $participant->ticketpivot->filter(function($ticketpivot){
    
                return in_array( $ticketpivot->ticket->role, $this->allowedRoles);
    
            });

            if(!$participant->ticketpivot->count()){
                $item->participants->forget($participant->id); 
            }


        }




        return $item;
        
    }


}