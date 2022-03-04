<?php

namespace Eventjuicer\Crud\Traits;

use Eventjuicer\Models\Group;

trait UseActiveEvent {

    public function activeEventId(){
        
        $this->setData();

        $group_id = $this->getParam("x-group_id");

        return Group::findOrFail($group_id)->active_event_id;
    }

}
