<?php

namespace Eventjuicer\Jobs\Posts;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


//custom
use Eventjuicer\Repositories\Admin\OrganizerPosts;
use Contracts\JobNotifier;
use Eventjuicer\Post;

class PublishArticle extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrganizerPosts $posts, JobNotifier $notify)
    {
        $posts->updateFlags(array("is_published" => 1), $this->post->id);

        $notify->from('editorapp')->to('#redakcja')->send('New post published! *' . e($this->post->meta->headline) . "*");

    }
}
