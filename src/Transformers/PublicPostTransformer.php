<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Post;

class PublicPostTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Post $post)
    {
      
        return [
           "id"             => $post->id,
           "is_sticky"      => $post->is_sticky,
           "is_promoted"    => $post->is_promoted,
           "created_at"     => (string) $post->created_at,
           "updated_at"     => (string) $post->updated_at,
           "published_at"   => (string) $post->published_at, 
           "author_id"      => $post->admin_id,
           "title"          => $post->meta->headline,
           "teaser"         => $post->meta->quote,
           "content"        => $post->meta->body
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
