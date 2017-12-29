<?php namespace Eventjuicer\Services;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Services\Hashids;

use Intervention\Image\ImageManager;

use Eventjuicer\Services\ParticipantPromoCreatives;


class PromoImageAddText {
	
	
	protected $template, $creatives;
	
	protected $target;

	protected $image;

	protected $fonts = [

		"bold" => '/assets/fonts/Roboto/Roboto-Bold.ttf',
		"medium" => '/assets/fonts/Roboto/Roboto-Medium.ttf',
		"black" => '/assets/fonts/Roboto/Roboto-Black.ttf',
		"normal" => '/assets/fonts/Roboto/Roboto-Regular.ttf',
	];

	protected $areas = [];


	static $fieldPattern = "@\[\[(?P<full>(?P<name>[a-zA-Z0-9_\-]+)(\?(?P<options>[a-z0-9=_\-&;]+)|))\]\]@i";

/*


An algorithm for the intersection detection ("overlapping") of any number of rectangles could work as follows. Two data structures are used.

A sorted list S of the x-coordinates of the left and right edges of the rectangles.
An interval tree T of the intervals given by the y-coordinates (bottom/top) of the rectangles.
The algorithm sweeps over the sorted list S of the x-coordinates:

If the current x-coordinate is the left edge of a rectangle R then the y-coordinates [y1, y2] of R are compared to the interval tree T. If an overlap is found, then the algorithm stops and reports OVERLAP. If there was no overlap in the tree T, then the interval [y1, y2] is inserted into the tree.
If the current x-coordinate is the right edge of a rectangle R then its y-interval [y1, y2] is removed from the interval tree T.
If the sorted list S is completely processed, then there was no overlap. The algorithm stops and reports NO-OVERLAP.
The time complexity for N rectangles is O(N*log(N)) because for each 2*N x-coordinates a search in the interval tree for N intervals is performed. The space complexity is O(N) for the auxiliary data structures S and T.



*/
	function __construct(ParticipantPromoCreatives $creatives, $target = false)
	{
		
		$this->creatives = $creatives;

		$this->template = $creatives->current()->template;

		$this->target = $target;

		$localPath = public_path("templates/" . $this->template->template_path);

		$this->file  	=  (new ImageShared(

			$localPath

		))->getImage();		

		$this->image = ( new ImageManager() )->make($this->file);		
	
	}


	protected function height()
	{
		return $this->image->height();		
	}

	protected function width()
	{
		return $this->image->width();
	}

	protected function stepX($multiple = 1)
	{
		return (int) round($this->height() / 100) * min($multiple, 100);
	}

	protected function stepY($multiple = 1)
	{
		return (int) round($this->height() / 100) * min($multiple, 100);
	}

	protected function getCenter($newObjectWidth)
	{
		return ($this->width() - $newObjectWidth) / 2;
	}

	protected function getMiddle($newObjectHeight)
	{
		return ($this->height() - $newObjectHeight) / 2;
	}

	protected function getX($newObjectWidth, $x)
	{
		return (int) max(0, $this->getCenter($newObjectWidth) +  $this->stepX($x));
	}

	protected function getY($newObjectHeight, $y)
	{
		return (int) max(0, $this->getMiddle($newObjectHeight) + $this->stepY($y));
	}	

	public function insertImage($urlOrPath, $x = 0, $y = 0, $w = 75, $h = 75)
	{

		$w = !empty($w) ? $this->stepX($w) : $this->stepX(75);

		$h = !empty($h) ? $this->stepY($h) : $w * 0.75;

		$insert = (
			(new ImageEncode($urlOrPath))->resize($w, $h)
		);

		$this->image->insert($insert, "top-left", $this->getX($insert->width(), $x), $this->getY($insert->height(), $y));

	}

	public function prepareText($text, $s = 10, $c = "#000000")
	{
		$font = new \Intervention\Image\Gd\Font($text);
		$font->file( $this->getFont("normal") );
		$font->size($s);
		$font->color($c);
		$font->align('left');
		$font->valign('top');
		return $font;
	}


	public function insertText($text, $s = 10, $c = "#000000", $x =0, $y = 0)
	{

		$s = $this->stepY($s);

		$t = $this->prepareText($text, $s, $c);

		$ts = $t->getBoxSize();
		
		//automagically DECREASE text size ... max is 90% of w/h

		while($ts["width"] > $this->width() * 0.9 || $ts["height"] > $this->height() * 0.9)
		{
			$s = $s * 0.9;
			$t = $this->prepareText($text, $s, $c);
			$ts = $t->getBoxSize();
		}

		$t->applyToImage($this->image, $this->getX($ts["width"], $x), $this->getY($ts["height"], $y) );
	}


	public function build()
	{

		foreach($this->template->data as $mod)
		{


			$x = isset($mod["x"]) ? (int) $mod["x"] : 0;
			$y = isset($mod["y"]) ? (int) $mod["y"] : 0;

			// as %
			$w = isset($mod["w"]) ? (int) $mod["w"] : 75;
			$h = isset($mod["h"]) ? (int) $mod["h"] : 0;

			$s = isset($mod["s"]) ? (int) $mod["s"] : 10;
			$c = isset($mod["c"]) ? $mod["c"] : "#000000";

			$text = !empty($mod["text"]) ? $mod["text"] : "";

			switch($mod["insert"])
			{

				case "logotype":

					$image = $this->creatives->getPromo()->participantImage();

					$this->insertImage($image, $x, $y, $w, $h);

				break;


				case "text":

					$text = $this->personalize($text);

					$this->insertText($text, $s, $c, $x, $y);

				break;

			}


	
		}

		if($this->target)
		{

			$this->image->save($this->target, 90);

			return file_exists($this->target);

		}

		
		// if($this->stream === true)
		// {
  		//  return response()->outputImage( $image->encode("png") );
		// }
	
		
	}



	protected function personalize($str = "")
	{
				
		if(strstr($str, "[[")!==false)
		{
			
			$obj = $this; 
			
			$str = preg_replace_callback(self::$fieldPattern, function($arr = array()) use($obj)
			{ 					
			

				$fieldName = trim(array_get($arr, "name"));

				return $obj->creatives->getPromo()->field($fieldName);

		 	}, $str);
		
		}
		
		
		return $str;
		
	}/*eom*/


	protected function getFont($weight = "bold")
	{
		return dirname(dirname(__FILE__)) . $this->fonts["bold"];
	}


}
