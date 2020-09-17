<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\CompanyData;

class ImageUrlWasProvided extends Event
{
    public $model;
 
    public function __construct(CompanyData $model) {
        $this->model = $model;

    }
    
}
