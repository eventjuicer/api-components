<?php

namespace Eventjuicer\Jobs\Posts;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//custom

use Illuminate\Foundation\Bus\DispatchesJobs;
use Jobs\Posts\PublishArticle;

use Eventjuicer\Repositories\Admin\OrganizerPosts;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\ColumnValidDateTime;
use Eventjuicer\Repositories\Criteria\ColumnGreaterThanZero;


use Carbon\Carbon;


class PublishPlannedArticles extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    use DispatchesJobs;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrganizerPosts $posts)
    {
        //1-already published, 2-set for publishing
        $posts->pushCriteria(new FlagEquals("is_published", 2));

        //we are skipping without published_at 
        $posts->pushCriteria(new ColumnValidDateTime("published_at"));

        //we are skipping posts without target portal defined
        $posts->pushCriteria(new ColumnGreaterThanZero("group_id"));

        foreach($posts->all() AS $post)
        {
            //timezone should come from organizer context / settings !

            if($post->shouldBePublished() ) 
            {
                $this->dispatchNow( new PublishArticle($post) );
            }
        }

    }






}