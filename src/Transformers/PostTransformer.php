<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\Post;

class PostTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Array $post)
    {
       
        return [
           "id" => 1
        ];
    }

    // public function includePurchases(Post $post)
    // {
    //     return $this->collection($participant->purchases, new PurchaseTransformer);
    // }

    // public function includeProfile(Participant $participant)
    // {
    //     $fields = $participant->fields->pluck("field_value")->toArray();

    //     return $this->item($fields, new ProfileTransformer);
    // }

}
