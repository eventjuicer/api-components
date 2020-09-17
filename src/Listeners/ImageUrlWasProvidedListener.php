<?php

namespace Eventjuicer\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


use Eventjuicer\Events\ImageUrlWasProvided;
use Eventjuicer\Jobs\SendImageToCloudinaryJob;

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
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
  //  public $connection = 'sqs';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
//    public $queue = 'listeners';


    /**
     * Create the event listener.
     *
     * @return void
     */
   

    public function __construct(){}

    /**
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */

    public function handle(ImageUrlWasProvided $event)
    {               
        dispatch( new SendImageToCloudinaryJob( $event->model, $event->base64 ) );
    }
}
