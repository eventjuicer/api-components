<?php

namespace Eventjuicer\Services;



use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Services\Personalizer;

use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Company;

class CompanyData {
	
	protected $companyDataRepo;
    protected $company;

	protected $names = [
        "name"                  => 1,
        "about"                 => 1, 
        "products"              => 1,
        "expo"                  => 0, 
        "keywords"              => 1,
        "website"               => 1,


        "facebook"              => 1,
        "twitter"               => 1,
        "linkedin"              => 1,
        "xing"                  => 1,

        "logotype"              => 1,
        "countries"             => 1,
        "opengraph_image"       => 0,
        "lang"                  => 0,

        "event_manager"         => 0,
        "pr_manager"            => 0,
        "sales_manager"         => 0,
   
        //"marketing_person"      => 0,
        "invitation_template"   => 0


    ];

    protected $namesInternal = [
        "logotype_cdn",
        "opengraph_image_cdn",
      
    

        /** api resource limits */
        "ranking_tweak",
        "invitations_tweak",
        "vip_tweak",

        "contributor",
        "password",
        "shared_image"
    ];


	function __construct(CompanyDataRepository $companyDataRepo) {
		$this->companyDataRepo = $companyDataRepo;
	}


    public function setValidations(array $validations){

        $this->names = $validations;

    }

	public function make(Company $company) {
    
        if( ! $this->isUpToDate( $company ) )
        {
            $company->fresh();
        }

        return $company->data;
	}



    public function lang(Company $company ) {

        $profile = $this->toArray($company);

        return !empty($profile["lang"]) ? $profile["lang"] : false;
    }


    public function toArray(Company $company ) {

        return $company->data->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->value];

        })->all();
    }

	
	protected function isUpToDate(Company $company ) {

        $presentDataFields = $this->toArray($company);
      

        foreach(array_diff_key( $this->names,  $presentDataFields ) as $name => $value)
        {
            
            $this->addField($company, $name, "company");
        }

        foreach(array_diff_key( array_flip($this->namesInternal),  $presentDataFields ) as $name => $value)
        {
            
            $this->addField($company, $name, "admin");
        }

        return false;

    }

    protected function addField(Company $company, $name, $access = "company" )
    {

            //double check if not exists

            $exists = $this->companyDataRepo->findWhere([
                "name" => $name,
                "company_id" => $company->id
            ])->count();

            if($exists)
            {
                return;
            }

            $companydata = $this->companyDataRepo->makeModel();

             $data = [

                "organizer_id" => $company->organizer_id,
                "group_id" => $company->group_id,      
                "company_id" => $company->id,
                "access" => $access,
                "name" => $name,
                "value" => ""
            ];

            $this->companyDataRepo->saveModel($data);
    }













   
}