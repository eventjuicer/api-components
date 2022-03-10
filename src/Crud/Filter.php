<?php

namespace Eventjuicer\Crud;


use Eventjuicer\Crud\Traits\UseRequestInfo;
use Eventjuicer\Crud\Traits\UseActiveEvent;
use Eventjuicer\Crud\Traits\UseRouteInfo;

abstract class Filter {

    use UseRequestInfo;
    use UseActiveEvent;
    use UseRouteInfo;

}