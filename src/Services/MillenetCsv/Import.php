<?php

namespace Eventjuicer\Services\MillenetCsv;

use Maatwebsite\Excel\Excel;



class Import {
	
	protected $excel;

	protected $columns;

	function __construct(Excel $excel)
	{
		$this->excel = $excel;
	}


	function get()
	{
		$rows = $this->excel->load(storage_path("Historia_transakcji_20170102_000656.csv"))->all();
	
		$this->columns = $rows->first()->keys()->toArray();
	}


}