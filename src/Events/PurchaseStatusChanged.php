<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\Purchase;

class PurchaseStatusChanged extends Event
{
    public $model, $status;

    public function __construct(Purchase $model, string $status)
    {
        $this->model = $model;
        $this->status = $status;
    }
    
}
