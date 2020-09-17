<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\CompanyData;

class ImageUrlWasProvided extends Event
{
    public $model;
    public $base64;

    public function __construct(CompanyData $model, $base64="") {
        $this->model = $model;
        $this->base64 = $base64;
    }
    
}
