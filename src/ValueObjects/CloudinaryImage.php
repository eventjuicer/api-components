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

	protected function hasCloudinaryModParams(){

	}

	function __toString()
	{
		return (string) $this->path;
	}




}