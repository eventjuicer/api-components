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
    public $base64 = "";

    public function __construct(Model $companydata, $base64=""){
        $this->companydata = $companydata;
        $this->base64 = $base64;
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

        $validBase64 =  !empty($this->base64);// && base64_encode(base64_decode($this->base64, true)) === $this->base64;
     
        if(stristr($this->companydata->value, "http") === false || !$validBase64){
            //nothing to do!!!!
            return;
        }

        $pubName        = "c_" . $company->id . "_" . $this->companydata->name;

        if(!empty($this->base64)){
            $response = $image->upload($this->base64, $pubName);
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

        //validate Cloudinary response????

        $companydataRepo->pushCriteria(new BelongsToCompany($company->id));
        $companydataRepo->pushCriteria(new FlagEquals("name", $this->companydata->name . "_cdn"));

        $target = $companydataRepo->all()->first();

        if($target)
        {
            $target->value = $secureUrl;
            $target->save();
        }

        
        if($validBase64){
            $this->companydata->value = $secureUrl;
            $this->companydata->save();
        }

    }
}
