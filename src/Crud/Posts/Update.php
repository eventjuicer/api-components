<?php 

namespace Eventjuicer\Crud\Posts;

use Eventjuicer\Crud\Crud;

class Update extends Crud {


    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }
    
    public function update($id){

        // $data = $this->postData();
        
        // if(empty($data)){
        //     return $this->jsonError("api.errors.not_enough_data", 500);
        // }
        
        // $post = $posts->find($id);
        
        // if(!$post){
        //     return $this->jsonError("api.errors.not_found", 404);
        // }
        
        // //normalize data...
        // array_walk($data, function(&$val, $key){ 
        
        //     if( strpos($key, "is_")!==false || strpos($key, "_id")!==false ){
        //         $val = intval($val);
        //     }
        
        //     if(strpos($key, "_at")!==false){
                
        //         //important Carbon::parse(null) returns Carbon NOW...
        //         $_val = Carbon::parse( $val );
        
        //         if($val && $_val->year > 1990){
        //             $val = (string) $_val->toDateTimeString();
        //         }else{
        //             $val = null;
        //         }
        //     }
        
        // });
        
        // //TODO: solve problem... published_at is getting set when updating item (without publishing it)
        
        // /** 
        // * setting published_at for the first time!
        // * saved data was NULL but new data is not null
        // */
        
        // if(!$post->published_at && array_get($data, "published_at", null)){
        //     $data["published_at"] = (string) Carbon::parse( array_get($data, "published_at"), "Europe/Warsaw" )->setTimezone("UTC")->toDateTimeString();
        // }
        
        // /**
        // * we set published flag.... but there is no published_at data. lets assume it should be published now!
        // */
        // if( array_get($data, "is_published") && !array_get($data, "published_at", null)){
        //     $data["published_at"] = (string) Carbon::now("UTC")->toDateTimeString();
        // }
        
        // $metadata = array_get($data, "meta", []);
        // unset($data["meta"], $data["company"], $data["images"], $data["cover"]);
        // $post->fill($data);
        
        // if(!empty($metadata)){
        //     $post->meta->fill($metadata)->save();
        // }
        
        // $post->save();
        // $post->fresh();
        

    }

        

}
    