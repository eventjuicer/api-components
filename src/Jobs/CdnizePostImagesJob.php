<?php

namespace Eventjuicer\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Models\PostImage as Model;

class CdnizePostImagesJob extends Job {// implements ShouldQueue {
 
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
        

        $filepath = "/u/apps/eventjuicer-production/public" . $this->postimage->path;

        $resource = stristr($this->postimage->imageable_type, "User")===false ? "posts" : "users";
        $prefix = $resource . "/" . $this->postimage->imageable_id . "_";
        
        try{

            $response = $image->uploadLocalFile($filepath, $prefix);

            $secureUrl = array_get($response, "secure_url", "");

            if(!empty($secureUrl)){
                $this->postimage->path = $secureUrl;
                $this->postimage->cloudinary_uploaded = 1;
                $this->postimage->save();
            }

        }catch(\Exception $e){

        }

       

    }
}
