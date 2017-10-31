<?php namespace Eventjuicer\Services;


use Intervention\Image\ImageManager;

class ImageEncode {
	
	protected $urlOrPath;
	protected $file;
	protected $type;
	protected $image;
	protected $target;

	function __construct($urlOrPath, $target = false)
	{
		
		$this->urlOrPath = $urlOrPath;

		$this->target = $target;

		$this->file  = (new ImageShared($urlOrPath))->getImage();

		$this->image = (new ImageManager())->make($this->file);

		$canvas = (new ImageManager())->canvas($this->image->width(), $this->image->height(), '#ffffff');

		$this->image = $canvas->insert($this->image);

		//$this->image->opacity(100);
	}

	public function resize($width = 400, $height = 250)
	{

		$this->image->resize((int) $width, (int) $height, function ($constraint) {
    			$constraint->aspectRatio();
    			$constraint->upsize();
		});


		$this->save();

		
		return $this->image;
		
	}

	public function width()
	{
		return $this->image->width();
	}

	public function height()
	{
		return $this->image->height();
	}


	public function save()
	{

		if($this->target)
		{
			$this->image->save($this->target, 90);

			return file_exists($this->target);
		}

	}

}
