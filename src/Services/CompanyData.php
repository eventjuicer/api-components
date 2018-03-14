<?php

namespace Eventjuicer\Services;



use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Services\Personalizer;



class CompanyData {
	
	protected $companyDataRepo;


	protected $names = 

    [
        "name",
        "about", 
        "products",
        "expo", 
        "keywords",
        "website",
        "facebook",
        "twitter",
        "linkedin",
        "logotype",
        "countries"
    ];

    protected $mappings = [

        "name"      => "cname2",
        "about"     => "company_description",
        "website"   => "company_website",
        "logotype"  => "logotype"
    ];


	function __construct(CompanyDataRepository $companyDataRepo)
	{
		$this->companyDataRepo = $companyDataRepo;
	}

	public function make(Model $company)
	{
    
        if( ! $this->isUpToDate( $company ) )
        {
            $company->fresh();
        }

        return $company->data;
	}

    public function migrate(Model $participant)
    {
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

	
	protected function isUpToDate(Model $company )
    {
      
        $diff = $company->data->count() ? 
        array_diff($this->names, $company->data->pluck("name")->all()) : 
        $this->names;

        foreach($diff as $name)
        {
            $companydata = $this->companyDataRepo->makeModel();

            $data = [

                "organizer_id" => $company->organizer_id,
                "group_id" => $company->group_id,      
                "company_id" => $company->id,
                "access" => "company",
                "name" => $name,
                "value" => ""
            ];

            $this->companyDataRepo->saveModel($data);
        }

        return false;

    }
   
}