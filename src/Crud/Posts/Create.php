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

        // $data = $this->postData();

        // if(empty($data) || empty($data["category"]) || empty($data["company_id"]) || empty($data["meta"]) || empty($data["meta"]["headline"]) ){
        // return $this->jsonError("api.errors.not_enough_data", 500);
        // }
    
    
        // $post = new Post;
        // $post->category = (string) $data["category"];
        // $post->company_id = (int) $data["company_id"];
    
        // $company = Company::findOrFail($post->company_id);
    
        // //should be taken from ADMIN!!!!
        // $post->organizer_id = $company->organizer_id;
        // $post->group_id = $company->group_id;
        // $post->admin_id = $this->admin->getUserId();
    
        // $post->save();
    
        // $postmeta = new PostMeta;
        // $postmeta->headline   = array_get($data, "meta.headline", "");
        // $post->meta()->save($postmeta);
    
       
    }

}

