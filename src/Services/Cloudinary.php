<?php 

namespace Eventjuicer\Services;

use Cloudinary as CloudinaryBase;
use Cloudinary\Uploader;
use Eventjuicer\ValueObjects\UrlImage;
use Eventjuicer\ValueObjects\Url;

//use Eventjuicer\Events\ImageShouldBeRescaled;



/*

{
"public_id":"al2g0bw4cmlh7dvmrfsp",
"version":1523140051,
"signature":"72b2e4b13b477b9ae63ea0caeec0523c17dc78ba",
"width":225,
"height":67,
"format":"png",
"resource_type": "image",
"created_at":"2018-04-07T22:27:31Z",
"tags":[],
"bytes":60524,
"type":"upload",
"etag":"51cfee554bfa5db66b0be605d51d8058",
"placeholder":false,
"url":"http:\/\/res.cloudinary.com\/eventjuicer\/image\/upload\/v1523140051\/al2g0bw4cmlh7dvmrfsp.png",
"secure_url":"https:\/\/res.cloudinary.com\/eventjuicer\/image\/upload\/v1523140051\/al2g0bw4cmlh7dvmrfsp.png",
"original_filename":"unifiedfactory"
}


*/


class Cloudinary {


	protected $expects = [

	];


	function __construct()
	{
		$this->configure();
	}

	function configure(){

		CloudinaryBase::config([
                "cloud_name" => "eventjuicer", 
                "api_key" => env("CLOUDINARY_KEY"),
                "api_secret" => env("CLOUDINARY_SECRET") 
        ]);
	}


	public function uploadBase64($data, $name = "", array $options = [])
	{

		// if(! imagecreatefromstring(base64_decode($data))){
		// 	return false;
		// }

		$name = env("APP_ENV", "local") === "local" ? 'test_' . $name : $name;

		if(!empty($name))
		{
			$options["public_id"] = $name;
		}

		$response = Uploader::upload($data, $options);

		return $response;
		
		
	}

	public function uploadLocalFile($path, $prefix = ""){

		$options = [];

		if(!file_exists($path)){
			return [];
		}

		$file = file_get_contents($path);

		if(!$file){
			return [];
		}

		$array = explode('/', $path);
		end($array);
		$filename = key($array);
		
		$array = explode(".", $filename);
		reset($array);
		$filename_without_extension = key($array);

        $options["public_id"] = $prefix . $filename_without_extension;

		$response = Uploader::upload($path, $options);

		return $response;
		
	}

	public function upload($path, $name = "", array $options = [])
	{

		$url = new UrlImage(new URl($path));

		if(!$url->isValid())
		{
			return [];
		}

		$name = env("APP_ENV", "local") === "local" ? 'test_' . $name : $name;

		if(!empty($name))
		{
			$options["public_id"] = $name;
		}

		$response = Uploader::upload($path, $options);

		return $response;
		
		
	}

}