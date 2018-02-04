<?php

namespace Eventjuicer\Jobs\CompanyCampaign;


use Eventjuicer\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;


use Eventjuicer\Models\CompanyCampaign;
use Eventjuicer\Repositories\CompanyCampaignRepository;
use Eventjuicer\Repositories\CompanyContactlistRepository;


//use Eventjuicer\Contracts\Email\Templated as Mailer;
use Exception;
use Carbon\Carbon;

class ProcessImportData extends Job implements ShouldQueue
{


    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CompanyCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
     
    )
    {   


       
       
    }

    
    // public function failed(Exception $exception)
    // {
    //     // Send user notification of failure, etc...
    // }

}
