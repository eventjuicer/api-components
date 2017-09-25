<?php


namespace Eventjuicer\Services\ImageHandler;

use Closure;


use Image;
use Eventjuicer\Services\ImageHandler\Storage;


class Crop
{
 

    protected $image;
    protected $relative_source_path;
    protected $full_source_path;

    protected $params;
    protected $full_target_path;


    public function __construct( $path, array $params, $model, $overwrite)
    {

        if (! (int) $params['w'] || ! (int) $params['w'] )
        {
            throw new \Exception("Cropping requires at least w,h params!");
        }  

        $this->params               = $params;

        $this->full_target_path     = (string) new Storage($model, $path, $overwrite);

        $this->relative_source_path = $path;

        $this->full_source_path     = Storage::getFullPath($this->relative_source_path);

        if(file_exists($this->full_source_path))
        {
            $this->image                = Image::make( $this->full_source_path );

            $this->process();
        }

    }

    public function apply(Closure $func)
    {
        if($this->image)
        {
            $this->image = $func($this->image);
        }
    }


    private function process()
    {
        if($this->image)
        {
            $this->image->crop( 

                (int) $this->params["w"], 
                (int) $this->params["h"], 
                (int) $this->params["x1"],  
                (int) $this->params["y1"]  

            );  
        }
    }


    function __toString()
    {
        if($this->image)
        {
            $this->image->save( $this->full_target_path , 85);
            return (string) $this->full_target_path;
        }

        return "";
    }



}