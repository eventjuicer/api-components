<?php 

namespace Eventjuicer\Services;

use Cloudinary as CloudinaryBase;
use Cloudinary\Uploader;

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
		 CloudinaryBase::config([
                "cloud_name" => "eventjuicer", 
                "api_key" => env("CLOUDINARY_KEY"),
                "api_secret" => env("CLOUDINARY_SECRET") 
        ]);
	}


	public function upload($path, $name = "", $expect = "")
	{
		$options = [];

		if(!empty($name))
		{
			$options["public_id"] = $name;
		}

		$response = Uploader::upload($path, $options);

		// if( $expect && isset( $this->expects[$expect] ) )
		// {
		// 	//event( new ImageShouldBeRescaled() );
		// }

		return $response;
	}

}