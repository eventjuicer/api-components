<?php

namespace Eventjuicer\Jobs\Images;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

use Eventjuicer\Models\PostImage;

use Services\ImageHandler\Storage AS LocalImageStorage;

//use Log;

use Image;

use Config;


use Intervention\Image\Exception\NotReadableException;

use Services\ImageHandler\Exceptions\ImageNotFoundException;

//https://murze.be/2015/07/upload-large-files-to-s3-using-laravel-5/

//use Aws\S3\MultipartUploader;
//use Aws\Exception\MultipartUploadException;


class UploadFileToS3 extends Job implements ShouldQueue
{
     use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $image;

    public function __construct(PostImage $image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filepath = LocalImageStorage::getFullPath($this->image->path);

        if(!file_exists( $filepath ) )
        {
           // Log::error("UploadFileToS3 failed! Local image file does not exist!", ["image_id" => $this->image->id ]);

            throw new ImageNotFoundException();
        }

        //prepare metadata 
        try {
              $image = Image::make( $filepath  );
        }
        catch(\Exception $e)
        {
            throw new \Exception("Could not read file!");
        }
      

        $uploaded = Storage::disk('s3')->getDriver()->put($this->image->path,  fopen($filepath, 'r'), [ 'Metadata' => ["width"=>$image->width(), "height"=>$image->height(), "id" => $this->image->id ]]);

        if($uploaded)
        {
            $this->image->s3_uploaded = 1;
            $this->image->s3_bucket = Config::get('filesystems.disks.s3.bucket');
            $this->image->save();
        }
        else
        {
            throw new \Exception("Could not upload file to S3...dunno why :/");
        }

    }

    public function failed()
    {
        \Log::error("Upload to S3 failed", ["id"=>$this->image->id]);
    }


    //FOR FILES OVER 100MB
    public function uploadToS3($fromPath, $toPath)
    {
        $disk = Storage::disk('s3');
            $uploader = new MultipartUploader($disk->getDriver()->getAdapter()->getClient(), $fromPath, [
            'bucket' => Config::get('filesystems.disks.s3.bucket'),
            'key'    => $toPath,
        ]);

        try
        {
            $result = $uploader->upload();
            echo "Upload complete";
        }
        catch (MultipartUploadException $e)
        {
            echo $e->getMessage();
        }
    }





}