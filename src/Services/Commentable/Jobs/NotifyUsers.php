<?php

namespace Eventjuicer\Services\Commentable\Jobs;

use Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;



use Eventjuicer\Comment;

//use Log;

use Config;

//use Eventjuicer\Services\Commentable\Exceptions\ImageNotFoundException;

//https://murze.be/2015/07/upload-large-files-to-s3-using-laravel-5/

//use Aws\S3\MultipartUploader;
//use Aws\Exception\MultipartUploadException;


class NotifyUsers extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        \Slack::to('#dev')->send('@' . $this->comment->author->fname . ": " . $this->comment->comment );

    }

  

}