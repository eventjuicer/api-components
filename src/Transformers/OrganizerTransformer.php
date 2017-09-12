<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\Organizer;

class OrganizerTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'groups'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Organizer $organizer)
    {
        return [
            "id"    => (int) $organizer->id,
            "name"  => $organizer->name,
            "slug"  => $organizer->account,
            'links'   => [
                'self' => '/organizers/'.$organizer->id
             ]
               
        ];
    }

    public function includeGroups(Organizer $organizer)
    {
        return $this->collection($organizer->groups, new GroupTransformer);
    }
}
