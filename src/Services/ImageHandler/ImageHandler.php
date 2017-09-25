<?php


namespace Eventjuicer\Services\ImageHandler;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Config\Repository AS Config;


use Image;


use Eventjuicer\PostImage;

//MY COOL EXTENSIONS
use Jobs\Images\UploadFileToS3;
use Eventjuicer\Services\ImageHandler\Storage;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Eventjuicer\Services\Able;


use Contracts\Imageable AS Imageable;

use Eventjuicer\Services\ImageHandler\Importer;


class ImageHandler extends Able implements Imageable
{
   

    use DispatchesJobs;


    protected $request;

    protected $config;


    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;

        $this->config = $config["imagehandler"];
    }


    public function parse($text = "")
    {

        return new Importer($text = "");
    }


    public function storeAvatar($key = null, $params, $model = null, $attribute = null, $overwrite = false)
    {

        $width = is_array($params) && !empty($params["width"]) ? $params["width"] : 200;

        return $this->storeImage($key, $params, $model, $attribute, $overwrite, function($img) use ($width)
        {
            return $img->fit($width, $width, function ($constraint)
            {
                $constraint->upsize();  //prevent upsizing!
            });

        });
    }


    public function storeImage($key = null,  $params, $model = null, $attribute = null, $overwrite = false, $transform = null)
    {

        //TODO!!
        if($this->request->get('chunks', false))
        {
            return false; 
        }

        $params = $this->_mergeParams($params);

        $owner = $this->_getRecipient($params, $model);

        $handler = new Upload($key, $params, $owner, $overwrite);
        
        $handler->apply(function($img) use ($transform)
        {

            if($transform instanceof Closure)
            {
                return $transform($img);
            }

            if(intval($transform))
            {
                return $img->resize($transform, null, function ($constraint)
                {
                    $constraint->aspectRatio();
                    $constraint->upsize();  //prevent upsizing!
                });
            }

            return $img->resize(1920, null, function ($constraint)
            {
                $constraint->aspectRatio();
                $constraint->upsize();  //prevent upsizing!
            });

        });

        return $this->_storeImage((string) $handler, $owner, $attribute);
    }


    public function updateImage($image, $params, $model = null, $attribute = null, $overwrite = false, $transform = null)
    {

        if(is_numeric($image))
        {
            $image = PostImage::findOrFail($image);
        }

        $params = $this->_mergeParams($params);

        $owner = $this->_getRecipient($params, $model);

        $handler = new Crop($image->path, $params, $owner, $overwrite);
        
        $handler->apply(function($img) use ($transform)
        {

            if($transform instanceof Closure)
            {
                return $transform($img);
            }

            return $img->resize(1920, null, function ($constraint)
            {
                $constraint->aspectRatio();
                $constraint->upsize();  //prevent upsizing!
            });

        });

        return $this->_storeImage((string) $handler, $owner, $attribute);
    }


    private function _storeImage($filepath, $owner, $attribute)
    {   

            if(empty($filepath))
            {
                return false;
            }


            $path = Storage::getRelativePath($filepath);
    
            //CREATE NEW IMAGE
            $image = new PostImage();
            $image->organizer_id    = \Context::level()->get("organizer_id", $owner);
            $image->group_id        = \Context::level()->get("group_id", $owner);
            $image->event_id        = \Context::level()->get("event_id", $owner);
            $image->user_id         = \Context::user()->id() ;
            $image->path            = $path;
           
            //SAVE AND ATTACH
            $owner->images()->save($image);


            //Schema::hasColumn($model->getTable(), $column);

            if(!empty($attribute))
            {
                $owner->{$attribute} = $image->id;
                $owner->save();
            }

            $job = (new UploadFileToS3($image))->onQueue(
                env("QUEUE_SQS_URL") . env("QUEUE_SQS_NAME_IMAGES")
                );
            
            //UPLOAD TO S3
            $this->dispatch($job);
            


            return $image;
    }


    










    public function resize($path)
    {

        $img = Image::make($path);

        // prevent possible upsizing
        $img->resize(1920, null, function ($constraint)
        {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save();

    }

    public function uploadToS3()
    {





    }



    public function dimensions($path)
    {
         //TODO handle S3


        $img    = Image::make(public_path() . $path);

        return [ $img->width(), $img->height() ];

    }




    function import($url, $model)
    {
         $target_dir = (string) new Storage($model);
    }

       


    public function download($file, $model, Closure $closure)
    {

        $target_dir = (string) new Storage($model);

      //  $myFile = fopen('path/to/fole',"w") or die("Problems");
       // $client = new \GuzzleHttp\Client();
        //$request = $client->get('https://www.yourdocumentpage.com',['save_to'=>$myFile]);

    }

}