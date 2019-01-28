<?php

namespace Eventjuicer\ValueObjects;

use Closure;

class CloudinaryImage {

	protected $path;

	function __construct(string $path = "")
	{
		$this->path = new UrlImage( new Url( $path ));
	}

	public function isValid(){

		return strpos(
			(string) $this->path, "res.cloudinary.com")!==false;
	}

	public function thumb(){

		$str = (string) $this->path;

		if($this->isValid()){

			return str_replace("/image/upload/v", "/image/upload/w_600,h_300,c_fit/v", $str);
		}

		return $str;
	}

	public function version(){

		$test = preg_match("@/image/upload/([v0-9]+/[^\s]+)@i", (string) $this->path , $img_with_version);

		return $test && !empty($img_with_version[1]) ? $img_with_version[1] : false;

	}

	public function wrapped($template = "ebe_template_en")
	{

		if(!$this->isValid()){
			return $this->path;
		}
		//download template.... check dimensions...calculate stuff....

		return 'https://res.cloudinary.com/eventjuicer/image/upload/c_fit,h_270,w_800/u_'.$template.',y_-30/' . $this->version();
	}


	protected function hasCloudinaryModParams(){

	}

	function __toString()
	{
		return (string) $this->path;
	}




}