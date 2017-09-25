<?php


namespace Eventjuicer\Repositories;


use Bosnadev\Repositories\Contracts\RepositoryInterface;
//use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Services\Repository;

use Eventjuicer\Post;
use Eventjuicer\PostMeta;
use Eventjuicer\Portal;

use Context;

use Carbon\Carbon;

use Illuminate\Contracts\Cache\Repository as Cache;

use DB;


use Illuminate\Database\Eloquent\Collection;


use Eventjuicer\Repositories\Criteria\BelongsToOrganizer;
use Eventjuicer\Repositories\Criteria\BelongsToGroup;
use Eventjuicer\Repositories\Criteria\SortByDesc;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\TaggedWith;
use Eventjuicer\Repositories\Criteria\Limit;
use Eventjuicer\Repositories\Criteria\OlderThanDateTime;
use Eventjuicer\Repositories\Criteria\YoungerThanDateTime;
use Eventjuicer\Repositories\Criteria\RelTableHas;

class PortalPosts extends Repository {

	private $portal_id;

	private $recent;
	private $popular;	
	private $recent_paginated;

    protected $preventCriteriaOverwriting = false;


    



    public function model()
    {
        return Post::class;
    }


    
    public function monthly($months = 12)
    {

         return $this->cached(null, 5, function() use ($months)
        {
              return Post::select(\DB::raw("count(*) as num_of_posts"), \DB::raw("DATE_FORMAT(published_at, '%Y-%m') as yearmonth"))->where("group_id", $this->context->get("group_id"))->groupby("yearmonth")->orderby("yearmonth", "DESC")->take($months)->get();

        });


    }



   


    public function sticky($limit = 3)
    {

        $safeLimit = $this->correctLimit($limit);

        return $this->filter($this->cached(null, 5, function() use ($safeLimit)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            $repo->pushCriteria(new FlagEquals("is_sticky", 1));
            $repo->pushCriteria(new Limit($safeLimit));

            return $repo->with(["meta", "author", "images"])->all();

        }), $limit);

    }


    public function promoted($limit = 10)
    {



        $safeLimit = $this->correctLimit($limit);

        return $this->filter($this->cached(null, 5, function() use ($safeLimit)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            $repo->pushCriteria(new FlagEquals("is_promoted", 1));
            $repo->pushCriteria(new Limit($safeLimit));

            return $repo->with(["meta", "author", "images"])->all();

        }), $limit);

    }


    public function popular($limit = 10)
    {




        $safeLimit = $this->correctLimit($limit);

        return $this->filter($this->cached(null, 5, function() use ($safeLimit)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("interactivity"));
            $repo->pushCriteria(new Limit($safeLimit));
            return $repo->with(["meta", "author", "images"])->all();

        }), $limit);

    }

    public function recent($limit = 10)
    {

       

        $safeLimit = $this->correctLimit($limit);

        return $this->filter($this->cached(null, 5, function() use ($safeLimit)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            $repo->pushCriteria(new Limit($safeLimit));
            return $repo->with(["meta", "author", "images"])->all();

        }), $limit);
        
    }

    /*sitemap*/
    public function recentAll()
    {


        $repo = \App::make(__CLASS__);
        $repo->pushCriteria(new BelongsToGroup);
        $repo->pushCriteria(new FlagEquals("is_published", 1));
        $repo->pushCriteria(new SortByDesc("published_at"));
        return $repo->with(["meta", "author"])->all();


        return $this->filter($this->cached(null, 30, function()
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            return $repo->with(["meta", "author"])->all();

        }), $limit);
        
    }


    public function recentPaginated($perPage = 50, $page_id = 0)
    {

        return $this->cached(null, 30, function() use ($perPage)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));

            return $repo->with(["meta", "author", "images"])->paginate($perPage);
        });

    }


    public function byAuthor($perPage = 50, $admin_id = 0)
    {

        return $this->cached(null, 30, function() use ($perPage, $admin_id)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("admin_id", $admin_id));
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));

            return $repo->with(["meta", "author", "images"])->paginate($perPage);
        });

    }




    public function olderThanDateTime($till = "", $from = "", $perPage = 50)
    {
        return $this->cached(null, 5, function() use ($till, $from, $perPage)
        {

            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            $repo->pushCriteria(new OlderThanDateTime("published_at", $till));
            $repo->pushCriteria(new YoungerThanDateTime("published_at", $from));

            return $repo->with(["meta", "author", "images"])->paginate($perPage);
        });

    }


    public function tagged($tag, $perPage = 25)
    {

        return $this->cached(null, 5, function() use ($tag, $perPage)
        {

            $hash = md5( str_slug( trim($tag) ));

            $tag = \Eventjuicer\Tag::where("hash", $hash)->first();

            if(empty($tag))
            {
                return collect([]);
            }


            $repo = \App::make(__CLASS__);
            $repo->pushCriteria(new BelongsToGroup);
            $repo->pushCriteria(new FlagEquals("is_published", 1));
            $repo->pushCriteria(new SortByDesc("published_at"));
            $repo->pushCriteria(new RelTableHas("taggings", "tag_id", $tag->id, TRUE));
            return $repo->with(["meta", "author", "images"])->paginate($perPage);

        });

    }




    /*

  */


    /*



     $tags = ["chuj 5", "karwatka", "asd", "wywiad"];

      $tags = \Eventjuicer\Tag::with(['latestPosts' => function($query)
      {

            $query->where("editorapp_posts.group_id", \Context::level()->get("group_id"))->where("editorapp_posts.is_published", 1);


      }, "latestPosts.meta", "latestPosts.author"])->whereIn("name", $tags)->get()->keyBy("name");











        Category::whereHas('posts', function ($posts) {

            $posts->where('status', 'is_published')
            ->where('id', function ($sub) {
                $sub->from('posts as sub')
                ->selectRaw('max(id)')
             ->whereRaw('sub.category_id = posts.category_id')
        });
        });
        })->with('latestPost')->get()


      */


        /*

        //name taken from category!

        $tags = \Eventjuicer\Tag::with('latestPosts.meta', 'latestPosts.author')->whereHas('posttags', function ($posts)
        {

        $posts->where("portal_id",  \Context::level()->get("group_id"));

        })->whereIn("name", $tags)->get()->keyBy("name");


        $tags = ["chuj 5", "asd", "metallica"];

        */
        //  dd($tags);


        //i should rather work on pivot table?


        //   $tags = ["chuj 5", "asd", "metallica"];

        //  $tags = \Eventjuicer\Tag::with('latestPosts.meta', 'latestPosts.author')->whereIn("name", $tags)->get()->keyBy("name");


        ///now we load categories and build list of posts from their tags!


        //  dd($tags["asd"]->latestPosts); //WORKS!!!


        

	


	

    function dumb()
    {
        return new Post();
    }





    public function byId($post_id = 0)
    {
        return Post::with("meta", "author")->find( (int) $post_id);
    }


   














}
