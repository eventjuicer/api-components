<?php

namespace Eventjuicer\Jobs;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use Eventjuicer\User;


class AdminUserNewEmailWelcome extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        $user = $this->user;

        if(!$user->organizer->id)
        {
            throw \Exception();
        }

        $join_link = "http://" . $user->organizer->account . ".eventjuicer.com/login";

        \Mail::send('emails.userwelcome', compact("user", "join_link"), function ($m) use ($user, $join_link)
        { 
            $m->from('notify@eventjuicer.com', 'Event Juicer');
            $m->to($user->email,  $user->fname . " " . $user->lname)->subject('You are invited to join ' . $user->organizer->account . "!");
        
        });

    }
}
