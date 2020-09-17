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

    public function __construct(Model $companydata){
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
     
        if(stristr($this->companydata->value, "http") === false && empty($this->companydata->base64) ){
            //nothing to do!!!!
            return;
        }

        $pubName        = "c_" . $company->id . "_" . $this->companydata->name;

        if(!empty($this->companydata->base64)){
            $response = $image->uploadBase64($this->companydata->base64, $pubName);
        }else{
            $response = $image->upload($this->companydata->value, $pubName);
        }

        if(empty($response))
        {
            throw new \Exception('Cannot upload given resource to: ' . $pubName);
        }

        $secureUrl = array_get($response, "secure_url", "");

        //just in case :D

        $companydataPrepare->make($company);

        //CDN!!!

        $companydataRepo->pushCriteria(new BelongsToCompany($company->id));
        $companydataRepo->pushCriteria(new FlagEquals("name", $this->companydata->name . "_cdn"));
        $cdn = $companydataRepo->all()->first();

        if($cdn){
            $cdn->value = $secureUrl;
            $cdn->save();
        }

        
        if(!empty($this->companydata->base64)){
            $this->companydata->value = $secureUrl;
            $this->companydata->base64 = null;
            $this->companydata->save();
        }

    }
}
