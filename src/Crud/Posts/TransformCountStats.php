<?php

namespace Eventjuicer\Crud\Posts;

use Eventjuicer\Models\Post;
use Eventjuicer\Models\PostMeta;
use Eventjuicer\Repositories\PostRepository;
use Eventjuicer\Crud\Traits\Aggregate;

class TransformCountStats {

    use Aggregate;

    public $repo;

    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }

    public function transform(Post $item){

        /**
         * 
         * created_at
         * updated_at
         * published_at
         * 
         */
        
        if($item->is_published){
            $this->increment("published");
            $this->increment($item->category);
        }

        if($item->is_published && $item->is_promoted){
            $this->increment("promoted");
        }

        if($item->is_published && $item->is_sticky){
            $this->increment("sticky");
        }
        
        return $item;
        
    }


}