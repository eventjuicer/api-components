<?php

namespace Eventjuicer\Jobs\Meetups;

use Illuminate\Contracts\Queue\ShouldQueue;

use Eventjuicer\Models\Meetup;

use Eventjuicer\Contracts\Email\Templated as Mailer;


class SendMeetupRequestEmail extends Job implements ShouldQueue
{


    protected $meetup;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Meetup $meetup)
    {
        $this->meetup = $meetup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        
        

       
    }
}
