<?php 

namespace Eventjuicer\Services\Sender;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;

use Contracts\ImporterAdapter;

use Excel;

use Context;

use Illuminate\Support\MessageBag;


class File implements ImporterAdapter {
	

	protected $request, $config, $errors, $fieldname;



	protected $storage, $upload_path;

	protected $organizer_id, $user_id;

	protected $source, $filename, $filepath, $mimetype;
	
	protected $results = [];

	protected $description = "";



	public function __construct(Request $request, Config $config, MessageBag $errors, $fieldname = "")
    {

        $this->request = $request;
        $this->config = $config;
    	$this->errors = $errors;
        $this->fieldname = $fieldname;

        //$this->config = $config["imagehandler"];

 		$this->organizer_id = Context::level()->get("organizer_id");

        $this->organizer = Context::level()->get("organizer");

        $this->user_id = Context::user()->id();

        $this->storage = app('files');
        
        $this->upload_path =  storage_path( "upload/organizers/" . $this->organizer_id );

        set_time_limit(600);



        $this->import();
    }




    private function import()
    {

    	if(empty($this->fieldname))
    	{
    		throw new \Exception("You must provide request source");
    	}

    	if(! $this->request->file($this->fieldname)->isValid() )
        {

        	$this->errors->add($this->fieldname, "File is invalid!");

        	return false;

        }

       
        if(!$this->storage->isDirectory($this->upload_path ) )
        {
            $this->storage->makeDirectory($this->upload_path , 0770, true);
        }
     

        $this->filename     = $this->request->file($this->fieldname)->getClientOriginalName();
        $this->mimetype     = $this->request->file($this->fieldname)->guessExtension();
        $this->filepath     = $this->upload_path . "/" . md5( $this->filename );

        $this->request->file($this->fieldname)->move($this->upload_path, md5($this->filename));


        switch($this->mimetype)
        {

            case "xls":

                Excel::selectSheetsByIndex(0)->filter('chunk')->load( $this->filepath )->chunk(250, function($results)
                {
                        foreach($results as $row)
                        {
                            // do stuff
                        }
                });

            break;

            case "txt":
            case "csv":

                preg_match_all(VALID_EMAIL, file_get_contents($this->filepath) , $email_addresses);

                $this->results = (!empty($email_addresses[0]) && is_array($email_addresses[0])) ? $email_addresses[0] : array();

            break;

        }

        if(!empty($this->results))
        {
        	$this->description = $this->filename;
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