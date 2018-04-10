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
        "name"          => 1,
        "about"         => 1, 
        "products"      => 1,
        "expo"          => 1, 
        "keywords"      => 1,
        "website"       => 1,
        "facebook"      => 0,
        "twitter"       => 0,
        "linkedin"      => 0,
        "logotype"      => 1,
        "countries"     => 1,
        "opengraph_image" => 0
    ];

    protected $namesInternal = [
        "logotype_cdn",
        "opengraph_image_cdn" 
    ];


    protected $mappings = [

        "name"      => "cname2",
        "about"     => "company_description",
        "website"   => "company_website",
        "logotype"  => "logotype"
    ];


	function __construct(CompanyDataRepository $companyDataRepo) {
		$this->companyDataRepo = $companyDataRepo;
	}


	public function make(Company $company) {
    
        if( ! $this->isUpToDate( $company ) )
        {
            $company->fresh();
        }

        return $company->data;
	}


    public function migrate(Participant $participant) {
        $company = $participant->company;

        if(is_null($company))
        {
            return false;
        }

        $profile = (new Personalizer($participant))->getProfile();

        $updates = [];

        foreach($this->make($company) AS $data)
        {
            //check if value exhists...if not.. try to find in participants profile...

           if($data->value)
           {
                continue;
           }

           if(!isset($this->mappings[$data->name]))
           {
                continue;
           }


           $replacement = trim( array_get($profile, $this->mappings[$data->name]) );

           // if(mb_strlen($replacement))
           // {
           //      continue;
           // }

            $data->value = $replacement;
            $data->save();
            $updates[] = $data->name;
        }

        return $updates;

    }


    public function status(Company $company) {
        $this->make($company);

        $presentDataFields = $this->toArray($company);

        $errors = [];


        foreach(array_filter($this->names) as $name => $isRequired)
        {
            //get present value!

            $value = $presentDataFields[$name];

            switch($name)
            {
         
                case "website":
                case "facebook":
                case "twitter":
                case "linkedin":
                case "logotype":

                    if(!trim($value))
                    {
                        $errors[$name] = "empty";

                    }else{
                        
                        if(strpos($value, "http")===false) {
                             $errors[$name] = "badformat";
                        }
                    }

                   
                break;
                case "about":
                case "products":
                case "expo":

                    if(!trim($value) || strlen($value) < 20)
                    {
                        $errors[$name] = "empty";

                    }else{
                        
                        if($value == strip_tags($value)) {
                            $errors[$name] = "nohtml";
                        }
                    }

                break;
                default: 

                if(is_array($value))
                {
                    if(!count($value))
                    {
                        $errors[$name] = "empty";
                    }
                }
                else
                {
                    if(strlen($value) < 5)
                    {
                        $errors[$name] = "empty";
                    }
                }
            }
        }

        return $errors;
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