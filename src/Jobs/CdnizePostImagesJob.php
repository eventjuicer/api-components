<?php

namespace Eventjuicer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Models\PostImage as Model;

class CdnizePostImagesJob extends Job implements ShouldQueue {
 
    public $postimage;

    public function __construct(Model $postimage){
        $this->postimage = $postimage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Cloudinary $image){
        
        $source = file_get_contents("/u/apps/eventjuicer-production/public" . $this->postimage->path);

        if(!$source){
            return;
        }

        $oldFilename = end(explode('/', $this->postimage->path));

        $prefix = stristr($this->postimage->imageable_type, "User")===false ? "posts/" : "users/";
        $newFilename = $prefix . $this->postimage->imageable_id . "_" . $oldFilename;
        $response = $image->upload($source, $newFilename);

        if(empty($response))
        {
            throw new \Exception('Cannot upload given resource to: ' . $newFilename);
        }

        $secureUrl = array_get($response, "secure_url", "");

        $this->postimage->path = $secureUrl;
        $this->postimage->cloudinary_uploaded = 1;
        $this->postimage->save();

    }
}
