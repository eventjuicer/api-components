<?php

namespace Eventjuicer\Jobs;

use Eventjuicer\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Eventjuicer\Participant;
use Mail;

class SendNewsletterWelcomeEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $participant;
    protected $widget_id;

    public function __construct(Participant $participant, $widget_id)
    {
        $this->participant  = $participant;
        $this->widget_id    = $widget_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $p = $this->participant;

        Mail::send('emails.newsletterwelcome', compact("participant"), function($m) use ($p)
        { 
            $sender = $p->group->name;

            $m->from('notify+123@eventjuicer.com', $sender);
            $m->to("adam@zygadlewicz.com")->subject('You are invited to join');
        
        });
    
    }
}
