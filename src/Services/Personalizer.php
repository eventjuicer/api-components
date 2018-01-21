<?php


namespace Eventjuicer\Services;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\ValueObjects\EmailAddress;

class Personalizer {


	const VALID_FIELDNAME = "@\[\[(?P<full>(?P<name>[a-zA-Z0-9_\-]+)(\?(?P<options>[a-z0-9=_\-&;]+)|))\]\]@i";

	protected $links = array(

			// "branding" => "account/branding",
			// "ticket" => "http://targiehandlu.pl/ticket/" . $this->code,
			// "bilet" => "http://targiehandlu.pl/bilet/" . $this->code,
		);

	protected $original;
	protected $translated;
	protected $model;

	protected $profile;

 	function __construct(Model $model, $str, $replacements = [])
	{

		$this->model = $model;

		$this->original = $str;
		
		$this->profile = $model->fields->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->pivot->field_value];
        });

		if(strstr($this->original, "[[")===false)
		{
		
			$this->translated = $this->original;
		}

		$this->translated = preg_replace_callback(

			self::VALID_FIELDNAME, 

			function($arr = array()) use($model, $replacements)
			{ 		

				$key 			= strtolower(array_get($arr, "name"));
				

				if(array_get($arr, "options"))
				{
					parse_str(array_get($arr, "options"), $options);

				}



				// if(!empty($options) && getval($options, "name"))
				// {
				// 	return $obj->field($key)->label;
				// }

				// $has_link 		= strpos($key, "link_to_");			

				// if($has_link !== false && isset($links[str_replace("link_to_","",$key)]))
				// {
				// 	$output = $links[str_replace("link_to_","",$key)];
				// }


				if(isset($model->$key))
				{
					$output = $model->$key;
				}
				else
				{
					$output = (string) array_get($this->profile, $key)
				}

				if(!empty($options))
				{
					if(isset($options["obfuscate"]))
					{
						if((int) strpos($output, "@") >0)
						{
							$output = (string) (new EmailAddress($output))->obfuscated();
						}
					}
				}
	
			
				// if(!empty($options) && (int) getval($options, "cut") && strlen($output) > getval($options, "cut"))
				// {
				// 	$output = mb_substr($output, 0, getval($options, "cut"), "UTF-8") . "...";
				// }
				
				
				return $output;
			
		 	}, $this->original);
		
		
		
	}/*eom*/



    function __toString()
    {
    	return (string) $this->translated;
    }


}