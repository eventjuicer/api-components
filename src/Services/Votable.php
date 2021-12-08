<?php 

namespace Eventjuicer\Services;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Votable {

   
    function __construct(Request $request){}
    
    public function enrichCollection(Collection $coll){
        return $coll->transform(function($item){
            return $item->relationLoaded("votes") ? $this->fixVotes($item) : $item;
        });
    }

    public function fixVotes(Model $model){
        if(!isset($model->votes)){
            return $model;
        }
        $votes_override_field = $model->fieldpivot->where("field_id", 213)->first();
        $votes_override = !is_null($votes_override_field) ? (int) $votes_override_field->field_value : 0;
        $model->_votes = $model->votes->count() + $votes_override;
        return $model;
    }

    
}

