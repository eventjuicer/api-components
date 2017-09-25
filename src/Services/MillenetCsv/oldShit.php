<?php

class Backofficeimporter extends Backofficeadmin 
{
	static $prefix 					= "csv_";
	static $examples 				= 5; //how many examples from imported file?
	static $basepath 				= "outcome/import?stage=";
	static $templates 				= array();
	
	
	static $csv_required_columns 	= array(
											"issuedate",
											"description",
											"party",
											"amount",
											"currency",
											"type"
											); //default import mapping template
	
	static $csv_available_filters 	= array(
		"skip_empty"		=> "skip row when cell is empty",
		"skip_not_empty"	=> "skip row when cell is not empty",		
		"skip_equals"		=> "skip row when cell equals to",
		"skip_not_equals"	=> "skip row when cell not equals to",
		"skip_greater_than"	=> "skip row when greater than",
		"skip_less_than"	=> "skip row when less than"
		
	);
	
	static $csv_uniqueness 			= array("issuedate", "party", "amount"); 
	
	
	
	public $csv_stage 				= 0; //current import stage!
	public $csv_file 				= ""; //file :>
	public $csv_finalized 			= 0;
	
	
	//used in step 2
	public $csv_examples 			= array();
	public $csv_assignment 			= array(); //mappings...
	public $csv_filter 				= array(); //mappings...
	public $csv_filter_param 		= array();
	public $csv_skip_first_row 		= 0;
	
	
	public $csv_import_name			= ""; //assignments
	public $csv_import_template 	= 0;
	

	
	function __construct()
	{
		parent::__construct();	
			
		if(!session_id())
		{
			session_start();
		}

		$this->csv_stage = (int) $this->param("stage", 1);
		$this->read_templates();
		
	}/*eom*/
	

	function csvimport()
	{
		
		$this->restrict_to("outcome_import");
		$this->view = "backoffice.outcome.import.stage." . $this->csv_stage;
		
		$this->csv_import_menu = $this->chunk("csv_import_menu", array("csv_stage" => $this->csv_stage, "csv_file" => $this->csv_session("file")));
		$this->csv_session("finalized"); //some chunks may need it!
		
		switch($this->csv_stage)
		{
			case "1":
				if(!empty($_POST))
				{
					//lets check file correctnes,
					//if ok lets move to stage #2

					$csv_file 				= $this->csv_input("file");

					//validate
					if($csv_file && preg_match(VALID_URL, $csv_file) && (stristr($csv_file, ".txt") OR stristr($csv_file, ".csv")))
					{
						$this->csv_session("file", $csv_file);
						
						
						
						//restore settings from template!
						$this->apply_template($this->csv_input("import_template"));
						
						
						$this->csv_session("finalized", 0); //we start whole process from scratch
						
						//alles ok, jump to step 2
						return $this->jump_to_stage(2);
								
					}
					else
					{
						$this->flash = "Bad file!<br/>File must be valid url ending with .txt or .csv...";
					}
				}
	
				if(!$this->csv_session("file"))
				{
					$this->csv_session("finalized", 0); 
				}
				
			break;
			
			case "2":
			
				$this->get_examples();
				
				if(!empty($_POST))
				{
										
					
							
					$csv_assignment 	= $this->csv_input("assignment");
					$skip_first_row 	= $this->csv_input("skip_first_row");
										
					//validate
					if(!empty($csv_assignment))
					{
						$this->csv_session("assignment", 		$csv_assignment);
						$this->csv_session("skip_first_row",	$skip_first_row);						
						$this->csv_session("finalized", 		0); 
					}
				}
				
				//reload form data
				
				$this->csv_session("skip_first_row");
				$this->csv_session("import_template");
				$this->csv_session("skip_first_row");
				
				//validations
				
				if(!$this->csv_session("assignment") OR count(self::$csv_required_columns) > count($this->csv_session("assignment")))
				{			
					$this->flash = "Please check or set assignments";
					return;	
				}else
				{
					//we are fucking ready!!!!!

					$this->csv_session("finalized", 1);
					
					if(!empty($_POST))
					{
						return $this->jump_to_stage(3);
					}
				}
			
			break;
			
			case "3":
				
				$this->get_examples();
				
				if(!empty($_POST))
				{

					$csv_filter 		= $this->csv_input("filter");
					$csv_filter_param 	= $this->csv_input("filter_param");

					//validate
					if(!empty($csv_filter))
					{
						$this->csv_session("filter", 		$csv_filter);
						$this->csv_session("filter_param", 	$csv_filter_param);
						//$this->csv_session("finalized", 0); 
					}
				}

				//reload form data
				$this->csv_session("filter");
				$this->csv_session("filter_param");
				
				if(!empty($_POST))
				{
					return $this->jump_to_stage(4);
				}
				
				//no validations here!				
			break;
			
			case "4":
				
			
				if(!empty($_POST))
				{					
					if($this->save_outcome_items(getval($_POST, "o")))
					{
						return $this->jump_to_stage(5);
					}
				}
				
				$this->to_be_imported 	= $this->prepare_import();

				$this->items_skipped 	= count($this->get_raw_data()) - count($this->to_be_imported);

	
			break;
			
			case "5":
			
				$import_template 		= $this->csv_session("import_template");			
			
				$import_name = mb_substr($this->csv_input("import_name"), 0, 100, "UTF-8");
				
				if(!empty($_POST) && $import_name)
				{
					//save template to DB
					
					$data = array(

						"name"	  		=> $import_name,
						"organizer_id" 	=> Fp20::$organizer_id,
						"admin_id" 		=> $this->get_admin_id(),
						"import_type" 	=> "csv",
						"filehash"		=> sha1($this->csv_session("file")),

						"json_data" 	=> my_json_encode(array(

							"sourcefile"		=> $this->csv_session("file"),
							"assignment" 		=> $this->csv_session("assignment"),
							"skip_first_row"	=> $this->csv_session("skip_first_row"),
							"filter"			=> $this->csv_session("filter"),
							"filter_param"		=> $this->csv_session("filter_param")

						))
					);

					Quickie_Database::replace("ems_backoffice_outcome_import_templates", $data);
					

					return $this->jump_to_stage(6);

				}
			
				return; 
				
			break;
			
			case "6":
				
				$this->clean_import();
				
				return $this->jump_to_stage(1);
						
			break;
			
			default:
			
				$this->jump_to_stage(1);
			
			break;
		}
		
		if($this->csv_stage > 2)
		{
			$data = $this->get_raw_data();

			if(empty($data))
			{
				//most probably we havent event started!
				return $this->jump_to_stage(1);				
			}

			if(!$this->importable())
			{
				//some problems with assignments?
				return $this->jump_to_stage(2);				
			}
		}
		
		
			
	}/*eom*/
	
	private function item_hash($item = array())
	{		
		return sha1(Fp20::$organizer_id . _ . implode("_", array_intersect_key($item, array_flip(self::$csv_uniqueness))));
	}
	
	private function save_outcome_items($items = array())
	{
		
		$raw_data = $this->get_raw_data();
		
		
		if(empty($items))
		{
			return false;
		}
		
		$inserted = 0;
		
		$final_items = array_intersect_key($this->prepare_import(), $items);
		
		foreach($final_items AS $index => &$item)
		{
						
			$exists = Quickie_Database::row("ems_backoffice_outcome", array("hash" => $this->item_hash($item)));
			
			if(!$exists)
			{
				if(Quickie_Database::insert("ems_backoffice_outcome", array(
					"hash"		 	=> $this->item_hash($item),
					"organizer_id" 	=> Fp20::$organizer_id,
					"issuedate"		=> date("Y-m-d", strtotime(getval($item, "issuedate"))), 
					"party"			=> getval($item, "party"),
					"description"	=> getval($item, "description"),
					"amount"		=> abs(getval($item, "amount")),
					"dump"			=> isset($raw_data[$index]) ? my_json_encode($raw_data[$index]) : ""
				)))
				{
					$inserted++;
				}
			
			}
		}
		
		return $inserted;
		
	}
	
	private function get_examples()
	{
		$data = $this->get_raw_data();
	
		if(empty($data))
		{
			//most probably we havent event started!
			return $this->jump_to_stage(1);				
		}
		
		$this->csv_examples = array_intersect_key($data, array_flip(array_rand($data, self::$examples)));
		//always add the first ROW to determine if we should skip it later :>
		array_unshift($this->csv_examples, $data[0]);
	}/*eom*/
	
	private function read_templates()
	{
		
		$organizer_id = Fp20::$organizer_id;
		
		self::$templates = Quickie_Database::query("SELECT * FROM ems_backoffice_outcome_import_templates WHERE organizer_id = {$organizer_id} ORDER BY id DESC;");	
	
	}/*eom*/
	
	private function importable()
	{
		//do necessary checks!
		
		$is_finalized 	= $this->csv_session("finalized");
		
		$data 			= $this->prepare_import();
			
		//additional checks??
		
		return ($is_finalized && !empty($data));
	}
	
	private function filter_param_check($filter = "", $param = "", $input = "")
	{
		
		switch($filter)
		{
			case "skip_empty": 
				
				if(empty($input))
				{
					return false;
				}
			
			break;
			case "skip_not_empty": 
				
				if(!empty($input))
				{
					return false;
				}
					
			break;	
			case "skip_equals": 
				
				if($input == $param)
				{
					return false;
				}
				
			break;
			case "skip_not_equals": 
				
				if($input != $param)
				{
					return false;
				}
					
			break;
			case "skip_greater_than": 
				
				if($input > $param)
				{
					return false;
				}
					
			break;
			case "skip_less_than": 
				
				if($input < $param)
				{
					return false;
				}
				
			break;
			
		}
		
		return true;
	}
	
	
	private function prepare_import()
	{
		
		$data 			= $this->get_raw_data();
		$assignments 	= (array) $this->csv_session("assignment");
		$filters 		= (array) $this->csv_session("filter");
		$filter_params 	= (array) $this->csv_session("filter_param");
		
		$final = array();
			
		foreach($data AS $i => $row)
		{		
						
			$_row = array();
			
			//first we go through filters!
			
			foreach($filters AS $position => $filter)
			{
				$filter_param = isset($filter_params[$position]) ? $filter_params[$position] : false;
								
				if(!$this->filter_param_check($filter, $filter_param, $row[$position]))
				{
					//fuck the whole row!
					continue(2);
				}
			}

	
			foreach($assignments AS $position => $assignment)
			{			
				$_row[$assignment]	= $this->clear_val($row[$position]); 			
			}
			
			//TBC
			//should be rather case- or user-specific
			if(empty($_row["party"]))
			{
				$_row["party"] = $_row["description"];
			}
			
			$final[$i] = $_row;
		}
		
		return $final;
		
	}/*eom*/
	
	private function clear_val($input)
	{
		return str_replace(array("'", '"', "  "), "", $input);
	}
	
	private function jump_to_stage($stage_no = 0)
	{
		redirect(self::$basepath . (int) $stage_no);
	}/*eom*/
		
	private function csv_input($key = "")
	{
		if(empty($_POST)) return;
		
		$value = getval($_POST, self::$prefix . $key);
		
		if(is_array($value))
		{
			$value = $this->trim_array_values($value);
		}
		
		return $value;
	}	
		
	private function csv_session($key = "", $value = null)
	{
		if(!is_null($value))
		{
			$_SESSION[self::$prefix . $key] = $value;		
		}
		
		$value = getval($_SESSION, self::$prefix . $key, null);
		
		$this->{self::$prefix . $key} = $value;
		
		return $value;
		
	}/*eom*/
	
	private function apply_template($id = 0)
	{
		if(! (int) $id)
		{
			return;
		}
		
		$query = (array) Quickie_Database::row("ems_backoffice_outcome_import_templates", compact("id"));
		
		if($query)
		{
			$this->csv_session("import_template",	$id);	
			
			unset($query["sourcefile"]);
			
			$data = json_decode(getval($query, "json_data"), true);
			
			foreach($data AS $k => $v)
			{
				$this->csv_session($k, $v);
			}
		}
		
	}/*eom*/

	private function clean_import()
	{
		foreach($_SESSION AS $k => $v)
		{
			if(stristr($k, self::$prefix)!==false)
			{
				unset($_SESSION[$k]);
			}	
		}
	}/*eom*/

	private function get_raw_data()
	{
		if(!$this->csv_session("file"))
		{
			return array();
		}
		
		$filehash =  md5(self::$prefix . $this->csv_session("file")); //hashed filename
		
		if(!$this->csv_session($filehash))
		{
			$data = str_getcsv(file_get_contents($this->csv_session("file")), "\n");
			
			if(!empty($data))
			{
				$this->csv_session($filehash, $data);
			}else
			{
				return array();
			}
		}
		
		$data = $this->csv_session($filehash);
		
		foreach($data as &$row)
		{
			$row = str_getcsv($row);
		}
	
		return $data;
				
	}/*eom*/
	
}/*eoc*/