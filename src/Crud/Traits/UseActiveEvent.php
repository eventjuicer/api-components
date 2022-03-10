<?php

namespace Eventjuicer\Crud\Traits;

use Eventjuicer\Models\Group;

trait UseActiveEvent {

    final public function activeEventId(){
        
        $this->setData();

        $group_id = $this->getParam("x-group_id");

        return $group_id? Group::findOrFail($group_id)->active_event_id: 0;
    }

}
