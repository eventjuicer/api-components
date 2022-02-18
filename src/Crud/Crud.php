<?php

namespace Eventjuicer\Crud;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class Crud {

    protected $data = [];
    protected $transforms = [];
    protected $payload = [];

    protected function payload(){
        $this->payload = json_decode(app("request")->getContent(), true);
    }

    public function getUser(){
        return app("request")->user();
    }

    public function setData($data=null){
       
        if(is_array($data)){
            $this->data = array_merge($this->data, $data);
        }else{
            $this->data = array_merge($this->data, app("request")->all() );
        }

        if(!app()->runningInConsole()){
            $this->payload();
        }

    }

    public function getParam($key, $replacement=null){

        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        if(isset($this->payload[$key])){
            return $this->payload[$key];
        }
        return $replacement;
    }

    public function getParams(){
        return array_merge($this->data, $this->payload);
    }

    public function setTransform($obj){

        if(class_exists($obj)){
            $this->transforms[] = $obj;
        }
    }

    public function getTransformed(){

        return $this->transform($this->get());
    }

    protected function transform($coll_or_model){

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
            }

        }
      

        return $coll_or_model;
    }

 

}