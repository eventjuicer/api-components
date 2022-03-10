<?php

namespace Eventjuicer\Crud\Posts;
use Eventjuicer\Repositories\PostRepository;
use Eventjuicer\Crud\Crud;
use Eventjuicer\Crud\Posts\PostRequest;
// use Eventjuicer\Models\Company;


class Create extends Crud {

    protected $repo;

    function __construct(PostRepository $repo){
        $this->repo = $repo;
    }
    
    public function create(){


        $imageable_id = (int) $this->getParam("imageable_id", 0);
        $imageable_type = $this->getParam("imageable_type");
        $base64 = $this->getParam("base64", "");
        $path = $this->getParam("path", "");
  

    
    
       
    }

}

