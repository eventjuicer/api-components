<?php 

namespace Eventjuicer\Services\Sender;

use Contracts\ImporterAdapter;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

use Illuminate\Support\MessageBag;



class Textarea implements ImporterAdapter {
	
	protected $source;

	protected $organizer_id, $user_id;

	protected $errors;

	protected $description;

	protected $results = [];

	public function __construct(Request $request, Config $config, MessageBag $errors, $fieldname = "")
    {
        $this->request = $request;

        $this->errors = $errors;

        //$this->config = $config["imagehandler"];
    }


    public function setSource($source)
    {
    	$this->source = $source;
    }

	public function isValid(){}

	public function import()
	{

		if($this->request->input($this->source))
        {

            preg_match_all(VALID_EMAIL, $this->request->input($this->source) , $email_addresses);

            $this->results = (!empty($email_addresses[0]) && is_array($email_addresses[0])) ? $email_addresses[0] : array();

            if(empty($this->results))
            {
                $this->errors->add($this->source, "No email addresses found!");
            }
            else
            {
            	$this->description  = "manual (" . count($this->results) . ")";
            	 
            }

        }
        else
        {

        }


	}

	public function items()
	{
		return $this->results;
	}

	function __toString()
	{
		return $this->description ? $this->description . ", " : "";	
	}


}