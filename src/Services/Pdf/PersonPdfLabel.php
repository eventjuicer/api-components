<?php 

namespace Eventjuicer\Services\Pdf;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Services\Personalizer;

class PersonPdfLabel {
	
	protected $model;
	protected $directory;
	protected $path;
	protected $label;

	function __construct(Model $model, $directory = "public/temp")
	{

		$this->model = $model;

		$this->directory = trim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

	}

	function generate(){

		$user = $this->model;

		$profile = (new Personalizer($this->model));

		$this->path = app()->basePath( $this->directory . "ticket_".$profile->code.".pdf");
    
		$data = [
			"first"     => mb_strtoupper($profile->fname), 
            "second"    => mb_strtoupper($profile->lname), 
            "third"     => mb_strtoupper($profile->cname2),
            "code"      => "https://expojuicer.com/p/" . $profile->code,
            "ribbon"	=> $profile->isVip() ? "VIP" : null
		];
		

        $this->label = (new PdfLabel())->addPage()->make($data)->addPage()->make($data);

        return $this->label;
	}


	function save()
	{

		$this->generate();

       	$this->label->save($this->path);

       	$publicPath = str_replace(app()->basePath("public"), "", $this->path);

     	return url( $publicPath );

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