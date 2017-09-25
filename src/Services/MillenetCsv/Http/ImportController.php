<?php

namespace Eventjuicer\Services\MillenetCsv\Http;

use Illuminate\Http\Request;
use Eventjuicer\Http\Requests;
use App\Http\Controllers\Controller;


//custom

use Eventjuicer\Services\MillenetCsv\Import;
use Kris\LaravelFormBuilder\FormBuilder;
use Eventjuicer\Services\MillenetCsv\Http\UploadFileForm;


class ImportController extends Controller
{
	protected $import;

	function __construct(Import $import)
	{
		$this->import = $import;
		$this->middleware(["web", "auth"]);

	}

    function index()
    {
    	
    	$this->import->get();

    	return "tet";
    }

    function create(FormBuilder $formBuilder)
    {
    	$form = $formBuilder->create(UploadFileForm::class, [
            'method' => 'POST',
            'url' => route('admin.imports.store')
        ]);
    	
    	return view("millenetcsv::import", ["form" => $form, "app_logotype"=>1]);
    }

    function store(Request $request)
    {

    }

    function show()
    {
    	
    	return "tet";
    }
}
