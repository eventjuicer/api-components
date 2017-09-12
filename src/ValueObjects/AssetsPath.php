<?php 

namespace Eventjuicer\ValueObjects;

use Illuminate\Database\Eloquent\Model;

use Config;


class AssetsPath  
{


    private $pathInfo;

    private $external;

    public function __construct($pathInfo)
    {

        $this->pathInfo = $pathInfo;


        
    }


    public function path()
    {
        $region = trim(Config::get('filesystems.disks.s3.region'), ".");

        if(is_object($this->pathInfo) && $this->pathInfo instanceof Model)
        {


            if((int) $this->pathInfo->s3_uploaded)
            {

                if(empty($this->pathInfo->s3_bucket))
                {
                //log the shit!
                }

                if(env("CLOUDFRONT_IMAGES", false))
                {
                    return trim(env("CLOUDFRONT_IMAGES"), "/") . $this->pathInfo->path;
                }
                else
                {
                    return 'https://'.$this->pathInfo->s3_bucket.'.s3.'.$region.'.amazonaws.com' . $this->pathInfo->path;
                }

            }
            else
            {
                return url($this->pathInfo->path);
            }
        


        }

        return $this->pathInfo;

    }



    public function __toString()
    {
        return (string) $this->path();
    }

   

}