<?php


namespace Eventjuicer\Services\Cascaded\Drivers;


use Eventjuicer\Services\Cascaded\Cascaded;
use Contracts\Setting;
use Illuminate\Database\Eloquent\Model as Eloquent;


class Settings extends Cascaded implements Setting
{



    protected $relation = "settings";

    protected $model = "Eventjuicer\Setting";



}