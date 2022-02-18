<?php

namespace Eventjuicer\Crud\Posts;

use Eventjuicer\Models\Post;
use Eventjuicer\Models\PostMeta;
use Eventjuicer\Repositories\PostRepository;



class TransformCover {

    public $repo;

    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }

    public function transform(Post $item){

        $cover = $item->images->where("is_cover", 1)->first();
        
        if($item->cover_image_id && $item->images){
            $item->_cover = $item->images->where("id", $item->cover_image_id)->first();
        }else if($cover){
            $item->_cover = $cover->path;
        }else{
            $item->_cover = $item->images->first() ? $item->images->first()->path : "";
        }
        
        return $item;
        
    }


}