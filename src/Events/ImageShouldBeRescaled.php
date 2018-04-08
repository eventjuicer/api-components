<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\CompanyData;

class ImageShouldBeRescaled extends Event
{
    public $model, $data;

    public function __construct(CompanyData $model, array $data)
    {
        $this->model = $model;
        $this->data = $data;
    }
    
}
