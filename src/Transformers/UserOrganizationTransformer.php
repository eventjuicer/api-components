<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;

use Eventjuicer\Models\UserOrganization;

class UserOrganizationTransformer extends TransformerAbstract
{


    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(UserOrganization $organization)
    {
        return [
            "id" => (int) $organization->id           
        ];
    }

  


}
