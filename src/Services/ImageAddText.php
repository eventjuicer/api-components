<?php namespace Eventjuicer\Services;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Services\Hashids;

use Intervention\Image\ImageManager;

class ImageAddText {
	
	protected $template;
	protected $creatives;
	protected $target;
	protected $fonts = [

		"bold" => '/assets/fonts/Raleway/Raleway-Bold.ttf'
	];

	protected $width;
	protected $height;

	function __construct(Model $creatives, $target = false)
	{
		
		$this->creatives = $creatives;

		$this->template = $creatives->template;
		$this->target = $target;

		$this->file  	=  (new ImageShared(

			storage_path("app/public/" . $creatives->template->path)

		))->getImage();		
	
	}




	public function build()
	{


		$image = ( new ImageManager() )->make($this->file);		

		$this->width = $image->width();
		$this->height = $image->height();
		
	
		foreach($this->template->data as $mod)
		{


			$y = isset($mod["y"]) ? (int) $mod["y"] : 0;

			$fontSize = round($this->height / 6);

			$textPosition = round( ($this->height - $fontSize) / 2) + $y;


			if(isset($mod["text"]))
			{
				$image->text($mod["text"], round($this->width/10), $textPosition, function($font) use ($fontSize) {

				$font->file( $this->getFont("bold") );

				$font->size( $fontSize );

				$font->color('#000000');
				$font->align('left');
				$font->valign('top');

				});

			}


			if(isset($mod["image"]))
			{

				$insert = (new ImageEncode($mod["image"]))->resize($this->width / 1.5);

				//from left
				$x = ($this->width - $insert->width()) / 2;

				//from top
				$y = ($this->height - $insert->height()) / 2.5;

				//now we have to determine position :)

				$image->insert($insert, "top-left", (int) $x, (int) $y);

			}	

		}

		if($this->target)
		{

			$image->save($this->target, 90);

			return file_exists($this->target);

		}

		
		// if($this->stream === true)
		// {
  //           return response()->outputImage( $image->encode("png") );
		// }
		// else
		// {
			

			
		// }

	}



	protected function getFont($weight = "bold")
	{
		return dirname(dirname(__FILE__)) . $this->fonts["bold"];
	}


}
