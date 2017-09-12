<?php

namespace Eventjuicer\Transformers;

use League\Fractal\TransformerAbstract;



class ProfileTransformer extends TransformerAbstract
{

   

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Array $data)
    {
        return [
                "id" => "asd"
        ];
    }

    

}
