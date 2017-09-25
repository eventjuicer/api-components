<?php

namespace Eventjuicer\Services\Install\Jobs;

use Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


//custom
use Eventjuicer\Context as Model;
use Contracts\Context;

class InstallDefaultContexts extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

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
    public function handle(Context $context)
    {
        
        $organizer      = $context->level()->get("organizer");
        $organizer_id   = $organizer->id;

        foreach(["asd", "qwery"] AS $slug)
        {
            $model = \App::make(Model::class)->firstOrNew(compact("slug", "organizer_id"));
            $model->slug = $slug;
            $model->organizer_id = $organizer_id;
            $model->save();
        }

    }
}
