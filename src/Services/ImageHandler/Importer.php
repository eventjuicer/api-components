<?php

namespace Eventjuicer\Services\ImageHandler;


/*
use Eventjuicer\Services\ImportImage\Dropbox;
use Eventjuicer\Services\ImportImage\Cloudapp;
use Eventjuicer\Services\ImportImage\GoogleDrive;
*/


class Importer {

	protected $text;
	protected $params;

	function __construct($text_to_be_parsed = "")
	{
			//$text, array $params, $model, $overwrite

		//$this->text = $text;
		//$this->params = $params;

		 //$this->full_target_path = (string) new Storage($model, $this->file->getClientOriginalName(), $overwrite);

	}


	function cache_image_file()
	{

	}


	function parse($str = "")
	{


		return preg_replace_callback(VALID_URL, function($matches)
		{

			//dd($matches);

			return 111;

		}, $str);


		if(preg_match_all(VALID_URL, $str, $urls))
		{						
			foreach($urls[0] AS $url)
			{				


				//we could send request...check if we have landing page and parse meta?


				//translate cloud app urls		
				if(strpos($url, "/cl.ly/")!==false)
				{
					
					$cloudapp = new Cloudapp();			
		
				}

				//https://www.dropbox.com/s/9qffhp7rdgnswdm/Komornik%201.pdf?dl=0

				elseif(strpos($url, "dropbox.com/s/")!==false)
				{
					//outside PUBLIC
					//https://www.dropbox.com/s/mx80aofgpgrem0n/signature.png
					//https://dl.dropbox.com/s/mx80aofgpgrem0n/signature.png?dl=1
					
					$dropbox = new Dropbox();			

				
					
				}

				//https://drive.google.com/file/d/0B3eX7zxqJ9q_Z1plQ2lfM004Vjg/view?usp=sharing

				//https://docs.google.com/uc?id=0B3eX7zxqJ9q_Z1plQ2lfM004Vjg&export=download

				elseif(strpos($url, "drive.google.com/file")!==false)
				{
					$dropbox = new GoogleDrive();			
				}
				else
				{
					$this->cache_image_file($url);

					return 11111;
				}
			}
		}
	
		return 11111;

	}


  	function GetURL($URL)
    {
            $ch = curl_init($URL);

            curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);


            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

            curl_exec($ch);

            $code = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

            curl_close($ch);

            return $code;
    }



/*

	public function __call($what, $args)
	{

		$class_name = __NAMESPACE__. "\\" . ucfirst($what);

		if(!class_exists($class_name))
		{
			throw new InvalidBladeExtensionsHandlerException();
		}

			
		return forward_static_call_array(array($class_name, "parse"), $args);


	}
*/

}