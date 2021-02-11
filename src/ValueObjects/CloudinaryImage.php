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

		return strpos( (string) $this->path, "res.cloudinary.com") !== false;
	}

	public function thumb($width = 600, $height = 600){

		$str = (string) $this->path;

		if($this->isValid()){

			return str_replace("/image/upload/v", "/image/upload/w_".$width.",h_".$height.",c_fit,f_auto/v", $str);
		}

		return null;
	}

	public function version(){

		$test = preg_match("@/image/upload/([v0-9]+/[^\s]+)@i", (string) $this->path , $img_with_version);

		return $test && !empty($img_with_version[1]) ? $img_with_version[1] : false;

	}

	public function wrapped($template = "ebe5_template_en", $constraints = "c_fit,h_210,w_800", $placing = "y_10")
	{

		if(!$this->isValid()){
			return $this->path;
		}
		//download template.... check dimensions...calculate stuff....

		return 'https://res.cloudinary.com/eventjuicer/image/upload/'.$constraints.'/u_'.$template.','.$placing.'/' . str_replace(".svg", ".jpg", $this->version() );
	}


	protected function hasCloudinaryModParams(){

	}

	function __toString()
	{
		return (string) $this->path;
	}




}