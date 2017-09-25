<?php

namespace Eventjuicer\Services\UserInvitator\Jobs;

use Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

//use Illuminate\Support\Collection;

use Eventjuicer\Services\UserInvitator\UserInvitationResender AS Message;


class ResendUserInvite extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $invitation;

    public function __construct(  $invitation)
    {
       $this->invitation = $invitation;

    
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

           //  "to_email"  => $invitation->email,
             //   "to_name"   => $invitation->nickname,
           //     "join_link" => $join_link, 
              //  "inviter"   => $invitation->user->email,
               // "organization" => $organization->name

        $invitation = $this->invitation;

        $join_link = "http://" . $invitation->user->organizer->account . ".eventjuicer.com.local/join?invitation=" . $invitation->code;

        $data = array(

            "to_email"      => $invitation->email,
            "to_name"       => $invitation->nickname,
            "join_link"     => $join_link, 
            "inviter"       => $invitation->user->email,
            "organization"  => $invitation->user->organizer->name

        );

        \Mail::send('emails.userinvitation', $data, function ($m) use ($data)
        { 
            $m->from('notify@eventjuicer.com', $data["inviter"] . ' @ Event Juicer');
            $m->to($data["to_email"],  $data["to_name"])->subject('You are invited to join ' . $data["organization"] . "!");
        
        });

    }
 

    public function failed()
    {
        //costam

    }

}
