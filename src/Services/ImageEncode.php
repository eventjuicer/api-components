<?php namespace Eventjuicer\Services;

use GuzzleHttp\Client as Guzzle;
use Intervention\Image\ImageManager;


class ImageEncode {
	
	protected $url;
	protected $file;
	protected $type;


	function __construct($url)
	{
		
		if(strpos($url, "http")===false)
		{
			throw new \Exception("Bad image URL...");
		}

		$this->url = $url;

		$this->getImage();

	}

	

	protected function getImage()
	{

		$request = (new Guzzle())->request("GET", $this->url);

		$this->file = (string) $request->getBody();

		$this->type = finfo_buffer(finfo_open(), $this->file, FILEINFO_MIME_TYPE);

		if(strpos($this->type, "image/")===false)
		{
			throw new \Exception("This file is not an image!!!!");
		}

	}

	protected function resize(){


		$manager = new ImageManager();

		$image = $manager->make($this->file)->resize(400, null, function ($constraint) {
    			$constraint->aspectRatio();
		});

		return $image->encode('data-url');

	}

	function __toString()
	{
		return (string) $this->resize();
	}

}
