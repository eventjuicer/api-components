<?php

namespace Eventjuicer\Crud\Companies;

use Eventjuicer\Repositories\CompanyRepository;
use Eventjuicer\Models\Company;

class OnlyExhibitorRoleTransform {

    public $repo;
    

    function __construct(CompanyRepository $repo){
        $this->repo = $repo;
    }

    public function transform(Company $item){

        $item->participants = $item->participants->filter(function($registration) {

            $role = $registration->ticketpivot->pluck("ticket.role");

            return $role->contains("exhibitor") || $role->contains("presenter");


        });
        
        return $item;
        
    }


}