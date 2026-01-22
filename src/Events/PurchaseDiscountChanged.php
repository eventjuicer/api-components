<?php

namespace Eventjuicer\Events;

use Eventjuicer\Models\Purchase;

class PurchaseDiscountChanged extends Event
{
    public $purchase, $discount;

    public function __construct(Purchase $purchase, int $discount)
    {
        $this->purchase = $purchase;
        $this->discount = $discount;
    }
    
}
