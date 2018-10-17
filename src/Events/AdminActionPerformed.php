<?php

namespace Eventjuicer\Events;


class AdminActionPerformed extends Event
{
    public $model, $status;

    public function __construct( $model, string $status)
    {
        $this->model = $model;
        $this->status = $status;
    }
    
}
