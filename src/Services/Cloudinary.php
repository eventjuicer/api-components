<?php

namespace Eventjuicer\Services;

use Cloudinary as CloudinaryBase;
use Cloudinary\Uploader;



class Cloudinary {


	function __construct()
	{
		 CloudinaryBase::config([
                "cloud_name" => "eventjuicer", 
                "api_key" => env("CLOUDINARY_KEY"),
                "api_secret" => env("CLOUDINARY_SECRET") 
        ]);
	}


	public function upload($path, $name = "")
	{
		$options = [];

		if(!empty($name))
		{
			$options["public_id"] = $name;
		}

		return Uploader::upload($path, $options);
	}

}