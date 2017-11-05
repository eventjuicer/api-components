<?php namespace Eventjuicer\Services\Pdf;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Services\Hashids;

class PersonPdfLabel {
	
	protected $model;

	protected $directory;

	protected $path;

	protected $label;

	function __construct(Model $model, $directory = "public/temp")
	{

		$this->model = $model;

		$this->directory = trim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		$this->generate();

	}

	function generate(){

		$user = $this->model;

		$code = (new Hashids())->encode($user->id); 

		$this->path = app()->basePath( $this->directory . "ticket_".$code.".pdf");
    
        $this->label = (new PdfLabel())->addPage()->make([

            "first"     => $user->profile("fname"), 
            "second"    => $user->profile("lname"), 
            "third"     => $user->profile("cname2"),
            "code"      => $code

        ])

       ->addPage()->make([

            "first"     => $user->profile("fname"), 
            "second"    => $user->profile("lname"), 
            "third"     => $user->profile("cname2"),
            "code"      => $code

        ]);


	}


	function save()
	{

       	$this->label->save($this->path);

       	$publicPath = str_replace(app()->basePath("public"), "", $this->path);

     	 return url( $publicPath );

       // return "https://api.eventjuicer.com" . $publicPath;
	}


	function download()
	{
       	return $this->save();
	}


	function __toString()
	{
		 return (string) $this->path;
	}

}