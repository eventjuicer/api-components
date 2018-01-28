<?php

namespace Eventjuicer\Jobs\CompanyImport;


use Eventjuicer\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Eventjuicer\Models\CompanyImport;

//use Eventjuicer\Contracts\Email\Templated as Mailer;


class ProcessContacts extends Job implements ShouldQueue
{


    protected $import;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CompanyImport $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    

       
    }
}
