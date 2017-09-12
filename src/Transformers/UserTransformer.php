<?php

namespace Transformers;

use League\Fractal\TransformerAbstract;

use Models\User;


class UserTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'organizers'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            "id" => (int) $user->id,
            "fname" => $user->fname,
            "lname" => $user->lname,
            "email" => $user->email,
            "profile" => $user->profile,
           
        ];
    }

    public function includeOrganizers(User $user)
    {
        return $this->collection($user->organizations, new OrganizerTransformer);
    }




}
