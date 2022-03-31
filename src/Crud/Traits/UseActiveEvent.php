<?php

namespace Eventjuicer\Crud\Traits;

use Eventjuicer\Models\Group;

trait UseActiveEvent {

    final public function activeEventId(){
        
        $group = $this->activeGroup();

        return $group? $group->active_event_id: 0;
    }

    final public function activeGroup(){
        
        $this->setData();

        $group_id = $this->getParam("x-group_id");

        return $group_id? Group::findOrFail($group_id): null;
    }


}
