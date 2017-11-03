<?php namespace Eventjuicer\Services\Pdf;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Services\Hashids;

class PersonPdfLabel {
	
	protected $model;
	protected $path;

	function __construct(Model $model)
	{

		$this->model = $model;

	}

	function save()
	{
		$user = $this->model;

		$code = (new Hashids())->encode($user->id); 

        $label = new PdfLabel();
    
        $this->path = $label->addPage()->make([

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

        ])

        ->save("ticket_".$code.".pdf");

        return $this->path;
	}


	function __toString()
	{
		 return (string) $this->path;
	}

}