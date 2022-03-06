<?php

namespace Eventjuicer\Jobs;

//use Illuminate\Foundation\Bus\Dispatchable;
use Eventjuicer\Models\CompanyData;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Crud\CompanyData\Fetch;
use Eventjuicer\Events\RestrictedImageUploaded;


class SendImageToCloudinaryJob extends Job //implements ShouldQueue
{
    //use Dispatchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $companydata;

    public function __construct(CompanyData $companydata){
        $this->companydata = $companydata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Cloudinary $image, Fetch $fetch){

        $company        = $this->companydata->company;
     
        if( stristr($this->companydata->value, "http") === false && stristr($this->companydata->value, "data:image/") === false ){
            //nothing to do!!!!
            return;
        }

        $pubName = "c_" . $company->id . "_" . $this->companydata->name;
        $response = $image->uploadUrlOrBase64($this->companydata->value, $pubName);

        if(empty($response))
        {
            throw new \Exception('Cannot upload given resource to: ' . $pubName);
        }

        $secureUrl = array_get($response, "secure_url", "");

        $cdn = $fetch->getByCompanyIdAndName($company->id, $this->companydata->name . "_cdn");
        $cdn->value = $secureUrl;
        $cdn->save();

        if($image->isBase64($this->companydata->value)){
            $this->companydata->value = $secureUrl; //remove base64 string as it is not needed!
            $this->companydata->save();
        }

        event(new RestrictedImageUploaded( $cdn->fresh() ));
      
    }
}
