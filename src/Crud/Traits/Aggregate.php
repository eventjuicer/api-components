<?php

namespace Eventjuicer\Crud\Traits;

trait Aggregate {

    protected $aggregates = [];

    protected function initializeAgg($key){
        if(!isset($this->aggregates[$key])){
            $this->aggregates[$key] = 0;
        }
    }

    protected function increment($key){
       $this->initializeAgg($key);
       $this->aggregates[$key]++;
    }

    protected function decrement($key){
        $this->initializeAgg($key);
        $this->aggregates[$key]--;
    }

    public function getAgg(){
        return $this->aggregates;
    }


}