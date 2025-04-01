<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\Purchase;

class PurchaseStatusChanged extends Event
{
    public $purchase, $status;

    public function __construct(Purchase $purchase, string $status)
    {
        $this->purchase = $purchase;
        $this->status = $status;
    }
    
}
