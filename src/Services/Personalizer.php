<?php


namespace Eventjuicer\Services;

use Eventjuicer\ValueObjects\EmailAddress;
use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Services\Hashids;

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

	protected $replacements;

 	function __construct(Model $model, $str = "", $replacements = [])
	{

		$this->model = $model;

		$this->original = $str;

		$this->replacements = (array) $replacements;
		


		$this->profile = $this->model->fields->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->pivot->field_value];

        })->all();

		if(strstr($this->original, "[[")!==false)
		{
		
			$this->translated = $this->translate($str);
		}
		
	}/*eom*/


	public function getProfile()
	{
		return $this->profile;
	}


	public function __get($attr) {

		if(isset($this->profile[$attr]))
		{
			return $this->profile[$attr];
		}

		return $this->model->$attr;
	}

	public function __call($name, $arguments) {
       return call_user_func_array($this->model, $arguments);
    }

    public function filter(array $profileFieldNames)
    {
    	 return array_intersect_key($this->getProfile(), array_flip($profileFieldNames));
    }

	public function translate(string $str, $replacements = [])
	{

		$replacements = array_merge($this->replacements, (array) $replacements);


		return $this->translated = preg_replace_callback(

			self::VALID_FIELDNAME, 

			function($arr = array()) use ($replacements)
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


				if(isset($this->model->$key)) {
					$output = $this->model->$key;
				}
				else
				{
					$output = (string) array_get($this->profile, $key, "");
				}


				if( $key === "code" || $key === "hash" ) {

					$output = (new Hashids())->encode($this->model->id);
				}


				if(empty($output))
				{
					$output = is_scalar($replacements) ? 
								$replacements : 
								array_get($replacements, $key, "");
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
			
		 	}, $str);


	}



    function __toString()
    {
    	return (string) $this->translated;
    }


}