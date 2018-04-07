<?php

namespace Eventjuicer\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use Eventjuicer\Events\ImageUrlWasProvided;
use Eventjuicer\Services\Cloudinary;
use Eventjuicer\Repositories\CompanyDataRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;

use Eventjuicer\Services\CompanyData;

/*

{
"public_id":"al2g0bw4cmlh7dvmrfsp",
"version":1523140051,
"signature":"72b2e4b13b477b9ae63ea0caeec0523c17dc78ba",
"width":225,
"height":67,
"format":"png",
"resource_type": "image",
"created_at":"2018-04-07T22:27:31Z",
"tags":[],
"bytes":60524,
"type":"upload",
"etag":"51cfee554bfa5db66b0be605d51d8058",
"placeholder":false,
"url":"http:\/\/res.cloudinary.com\/eventjuicer\/image\/upload\/v1523140051\/al2g0bw4cmlh7dvmrfsp.png",
"secure_url":"https:\/\/res.cloudinary.com\/eventjuicer\/image\/upload\/v1523140051\/al2g0bw4cmlh7dvmrfsp.png",
"original_filename":"unifiedfactory"
}


*/
class ImageUrlWasProvidedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    protected $image, $companydataRepo, $companydataPrepare;

    public function __construct(
        Cloudinary $image, 
        CompanyDataRepository $companydataRepo, 
        CompanyData $companydataPrepare
    ){
        $this->image = $image;
        $this->companydataRepo = $companydataRepo;
        $this->companydataPrepare = $companydataPrepare;
    }

    /**
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */

    public function handle(ImageUrlWasProvided $event)
    {
        
        $companydata    = $event->model;
        $company        = $companydata->company;

        $pubName        = "c," . $company->id . "," . $companydata->name;

        $response = $this->image->upload($companydata->value, $pubName);

        //just in case :D

        $this->companydataPrepare->make($company);


        //validate Cloudinary response????

        $this->companydataRepo->pushCriteria(new BelongsToCompany($company->id));
        $this->companydataRepo->pushCriteria(new FlagEquals("name", $companydata->name . "_cdn"));

        $target = $this->companydataRepo->all()->first();

        if($target)
        {
            $target->value = array_get($response, "secure_url", "");
            $target->save();
        }

    }
}
