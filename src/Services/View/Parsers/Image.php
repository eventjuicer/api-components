<?php

namespace Eventjuicer\Services\View\Parsers;



//required

use Eventjuicer\Services\View\Parsers\AbstractParser;

use Eventjuicer\Services\View\Exceptions\ParserTargetElementNotFoundException;

use Eventjuicer\ValueObjects\AssetsPath;

//custom

use Eventjuicer\PostImage;


class Image extends AbstractParser 
{
	
	

	public function resolve()
	{
		$image_id = $this->getAttribute("id");

		$image = PostImage::find($image_id);

		if(!$image)
		{
			throw new ParserTargetElementNotFoundException("No image with ID {image_id} given");
		}
		
		return $image;
	}


	public function htmlize($data)
	{

		$caption = "";

		$code = '<img itemprop="image" class="img-responsive" src="'.( new AssetsPath($data) ).'" alt="" />';

		$link = $this->getAttribute("link");

		$caption = $this->getInnerText();

		if($caption)
		{		

			$sizing = $this->hasAttribute("class") ? $this->getAttribute("class") : "image-full";

			if($sizing != "image-full")
			{
				$wrap = '<div class="row">
							<div class="col-md-6">%s</div>
							<div class="col-md-6">%s</div>
						</div>';
			}
			else
			{
				$wrap = '<div>%s<br/><small>%s</small></div>';
			}


			switch($sizing)
			{

				case "image-left":
						
					$code = sprintf($wrap, $code, $caption);

				break;

				case "image-right":
					
					$code = sprintf($wrap, $caption, $code);

				break;

				default:
					$code = sprintf($wrap, $code, $caption);
				break;
			}
			
		}



		return  $code . "\n";
	}



/*

	static function parse($image_id = 0, $size = "", $link = "")
	{

	
		//TO BE IMPLEMENTED

		$image = PostImage::find($image_id);

		if(!is_numeric($image_id) OR empty($image->path))
		{

		//	\Log::info("Image with requested ID not found", ["image_id" => $image_id]);

			//shouldnt we throw an exception? :)
			return '<img src="http://www.placehold.it/800x400/EFEFEF/AAAAAA&amp;text=image+not+found" />';


		}
	
		$img = '<img src="http://'.HOST . $image->path.'"/>';	

		$host = \Context::level()->getParameter("host");

		if($link && preg_match(VALID_URL, $link))
		{

			if($host && strstr($link, $host) === false)
			{
				return '<a href="'.$link.'" target="_blank">' . $img . '</a>';
			}

			return '<a href="'.$link.'">' . $img . '</a>';
		}

		return $img;
	}
*/



}