<?php namespace Eventjuicer\Services;


class ImageAddText {
	

	protected $image;

	function __construct(string $image)
	{
		$this->image = $image;

		if(!file_exists($this->image))
		{
			throw new \Exception("File not found!");
		}
	}


	function addText()
	{

		$im = imagecreatefrompng($this->image);

		imagesavealpha($im, true); 

		if(!$im)
		{
			throw new \Exception("Provided image is broken!");
		}

		$black = imagecolorallocate($im, 0, 0, 0);
		$width = 36; // the width of the image
		$height = 36; // the height of the image
		$font = 2; // font size
		$digit = $i; // digit
		$leftTextPos = 19 - (strlen($digit)*3);
		$outputImage = "group_icon_".$digit.".png";
		imagestring($im, $font, $leftTextPos, 9, $digit, $black);
		imagepng($im, $outputImage, 0);
		imagedestroy($im);

	}


}