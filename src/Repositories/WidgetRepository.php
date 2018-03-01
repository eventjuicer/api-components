<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Widget;
use Eventjuicer\Repositories\Repository;

 

class WidgetRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Widget::class;
    }





}

