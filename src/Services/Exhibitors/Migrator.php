<?php 

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Services\Personalizer;

use Eventjuicer\Models\Participant;
use Eventjuicer\Models\Company;


class Migrator {

    protected $mappings = [

        "name"          => "cname2",
        "about"         => "company_description",
        "website"       => "company_website",
        "logotype"      => "logotype",
        "linkedin"      => "profile_linkedin",
        //ecommerceberlin....
        "xing"          => "xing_profile",
        "sales_manager" => "marketing",
        "pr_manager"    => "email3"
    ];


	function __construct(CompanyDataRepository $companyDataRepo) {
		$this->companyDataRepo = $companyDataRepo;
	}


	 public function migrate(Participant $participant, $force = false) {

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

           if(!isset($this->mappings[$data->name]))
           {
                continue;
           }

           $current_value = trim($data->value);

           $replacement = trim( array_get($profile, $this->mappings[$data->name]) );

           if(mb_strlen($replacement) === 0)
           {
                continue;
           }

           if(mb_strlen($replacement) ===  mb_strlen($current_value) )
           {
                continue;
           }

            //if there is a value and we only add new....
            if(mb_strlen($current_value) && !$force ){

                continue;
            }

            $data->value = $replacement;
            $data->save();
            $updates[] = $data->name;
        }

        return $updates;

    }


}