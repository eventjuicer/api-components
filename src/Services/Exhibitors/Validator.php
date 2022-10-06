<?php

namespace Eventjuicer\Services\Exhibitors;

use Eventjuicer\Services\Exhibitors\CompanyData;

class Validator {
	

	protected $names = [
        "name"                  => 1,
        "about"                 => 0, 
        "products"              => 0,
        // "expo"                  => 0, 
        "keywords"              => 1,
        "website"               => 1,


        "facebook"              => 0,
        "twitter"               => 0,
        "linkedin"              => 0,
        "xing"                  => 0,

        "logotype"              => 1,
        "countries"             => 0,
        "opengraph_image"       => 0,
        // "lang"                  => 0,

        // "event_manager"         => 0,
        // "pr_manager"            => 0,
        // "sales_manager"         => 0,
   
        //"marketing_person"      => 0,
        "invitation_template"   => 0
    ];

    protected $companydata;

	function __construct(CompanyData $companydata) {
		$this->companydata = $companydata;
	}


    public function setValidations(array $validations){

        $this->names = $validations;

    }

    public function status() {
        
        $presentDataFields = $this->companydata->companyData(); 

        $errors = [];

        foreach(array_filter($this->names) as $name => $isRequired)
        {
            //get present value!

            if(!isset($presentDataFields[$name]))
            {
                $errors[$name] = "empty";
                continue;
            }

            $value = $presentDataFields[$name];

            switch($name)
            {
         
                case "website":
                case "facebook":
                case "twitter":
                case "linkedin":
                case "xing":
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

                // case "event_manager":
                // case "pr_manager":
                // case "sales_manager":

                //     if(!trim($value))
                //     {
                //         $errors[$name] = "empty";

                //     }else{
                        
                //         if(strpos($value, "@")===false) {
                //              $errors[$name] = "noemail";
                //         }
                //     }

                // break;

                case "about":
                case "products":
                // case "expo":

                    if(!trim($value) || strlen($value) < 20)
                    {
                        $errors[$name] = "empty";

                    }else{
                        
                        if(strlen($value) > 300 && strpos($value, "*")===false) {
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
                    if(strlen($value) < 3)
                    {
                        $errors[$name] = "empty";
                    }
                }
            }
        }

        return $errors;
    }


   
}