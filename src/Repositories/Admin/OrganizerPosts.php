<?php 

namespace Eventjuicer\Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Services\Repository;

use Eventjuicer\Post;
use Eventjuicer\PostMeta;

use Eventjuicer\ValueObjects\UTCDateTime;

use Carbon\Carbon;

class OrganizerPosts extends Repository
{
   

    protected $preventCriteriaOverwriting = false;


    protected $flags = ["is_published", "is_sticky", "is_promoted", "group_id", "admin_id"];


    protected $defaults = array("is_sticky" => 0, "is_promoted" => 0);




    public function model()
    {
        return Post::class;
    }
 
    public function getFlags()
    {
        return $this->flags;
    }


    public function search($query = "")
    {


        return $this->model->mostUsedTags();


        return $this->cached(null, 15, function()
        {
           
        });
    }


    public function mostUsedTags($costam="")
    {

       //dd(Cache::remember());

        return $this->cached(null, 15, function()
        {
            return $this->model->mostUsedTags();
        });

    }
/*
    public function setQuoteAttribute($value)
    {

        
        $this->attributes['quote'] = strlen($value)<3 ? $this->lead() . "..." : $value;

    }
*/
    protected function createOrUpdate(array $data, $id = 0)
    {

        if((int) $id)
        {
            $post = Post::find($id);
        }
        else
        {
            $post = new Post;
        }


        $post->fill( array_merge($this->defaults, $data) );

        $post->group_id         = isset($data["group_id"]) ? (int) $data["group_id"] : 0;
        $post->organizer_id     = $this->context->get("organizer_id");
        $post->editor_id        = $this->usercontext->id();


        if(isset($data["published_at"]))
        {

            $published_at = new UTCDateTime($data["published_at"], "Europe/Warsaw");

            if($published_at->year > 1990)
            {
                $post->published_at = (string) $published_at->toDateTimeString(); //UTC :)
            }
            else
            {
                $post->published_at = (string) Carbon::now('UTC')->toDateTimeString();
            }
        }
        else if(!$id)
        {
            $post->published_at = (string) Carbon::now('UTC')->toDateTimeString();
        }

        if($id)
        {   
            if(isset($data["topic"]))
            {
                $post->topics()->sync($data["topic"], 
                ["organizer_id" => $this->context->get("organizer_id")]);
            }
            else
            {
                $post->topics()->detach();
            }
        }

        $post->save();

        if(isset($data["meta"]))
        {
            $this->saveMeta($data["meta"], $post->id);
        }

        return $post;

    }



    public function create(array $data)
    {
        return $this->createOrUpdate($data, 0);
    }

    public function update(array $data, $id, $attribute = "id")
    {
        return $this->createOrUpdate($data, $id);
    }


    private function saveMeta($data, $post_id = 0)
    {

        if(!$post_id)
        {
            return;
        }

        $postmeta = PostMeta::firstOrNew(compact("post_id"));
        $postmeta->fill( $data );
        $postmeta->post_id = $post_id;
        $postmeta->save();

        return $postmeta;
    }


    


}