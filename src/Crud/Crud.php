<?php

namespace Eventjuicer\Crud;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Crud {

    protected $data = [];
    protected $transforms = [];
    protected $payload = [];
    protected $aggregates = [];
    
    protected function payload(){
        $this->payload = json_decode(app("request")->getContent(), true);
    }

    public function getUser(){
        return app("request")->user();
    }

    public function setData($data=null){

        if(!app()->runningInConsole()){
            $this->data = array_merge($this->data, app("request")->all() );
            $this->payload();
        }

        if(is_array($data)){
            $this->data = array_merge($this->data, $data);
        }

    }

    public function getParam($key, $replacement=null){

        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        if(is_array($this->payload) && isset($this->payload[$key])){
            return $this->payload[$key];
        }
        return $replacement;
    }

    public function getParams(){
        return is_array($this->payload) ? array_merge($this->data, $this->payload) : $this->data;
    }


   
    public function setTransform($obj){

        if(class_exists($obj)){
            $this->transforms[] = $obj;
        }else{
            throw new \Exception("Transformer $obj class not found");
        }
    }

    public function getTransformed(){

        return $this->transform(
            $this->get()
        );
    }

    public function showTransformed($id){
        return $this->transform(
            $this->get($id)
        );
    }

    public function getAgg(){

        return $this->aggregates;
    }


    public function transform($coll_or_model){

        if(empty($this->transforms) || !is_array($this->transforms)){
            return $coll_or_model;
        }

        foreach($this->transforms AS $transformer){

            $instance = app()->make($transformer);

            if(!method_exists($instance, "transform")){
                throw new \Exception("No transform method");
            }

            if(is_a($coll_or_model, Model::class)){
          
                $coll_or_model = $instance->transform($coll_or_model);
            }

            if($coll_or_model instanceof Collection){
                $coll_or_model->transform(function($item) use($coll_or_model, $instance) {
                    return $instance->transform($item);
                 });

                if(method_exists($instance, "getAgg")){
                    $this->aggregates = array_merge(
                        $this->aggregates,
                        $instance->getAgg()
                    );
                }

            }


        }
      

        return $coll_or_model;
    }

 

}