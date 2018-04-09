<?php

namespace Eventjuicer\Jobs;


use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Foundation\Bus\Dispatchable;

use Eventjuicer\Models\CompanyData as Model;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Repositories\CompanyDataRepository;



use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Services\CompanyData;




class SendImageToCloudinaryJob extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $companydata;

    public function __construct(Model $companydata)
    {
        $this->companydata = $companydata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        Cloudinary $image, 
        CompanyDataRepository $companydataRepo, 
        CompanyData $companydataPrepare
    ){

        $company        = $this->companydata->company;

        $pubName        = "c_" . $company->id . "_" . $this->companydata->name;

        $response = $image->upload($this->companydata->value, $pubName);

        //just in case :D

        $companydataPrepare->make($company);


        //validate Cloudinary response????

        $companydataRepo->pushCriteria(new BelongsToCompany($company->id));
        $companydataRepo->pushCriteria(new FlagEquals("name", $this->companydata->name . "_cdn"));

        $target = $companydataRepo->all()->first();

        if($target)
        {
            $target->value = array_get($response, "secure_url", "");
            $target->save();
        }


    }
}
