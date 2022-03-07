<?php

namespace Eventjuicer\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Validator;

abstract class Crud {

    protected $data = [];
    protected $filters = [];
    protected $transforms = [];
    protected $payload = [];
    protected $aggregates = [];
    protected $errors = [];
    
    protected function payload(){
        $this->payload = json_decode(app("request")->getContent(), true);
    }

    public function isValid(array $rules = []){
        $validator = Validator::make($this->getParams(), $rules);
        $this->errors = $validator->errors();
        return $validator->passes();
    }

    public function errors(){
        return !empty($this->errors)? array_keys( $this->errors->toArray() ): [];
    }

    public function getUser(){
        return app("request")->user();
    }

    public function getCompany(){
        return $this->getUser()->company;
    }

    public function getCompanyParticipants(){
        return $this->getCompany()->participants->pluck("id")->all();
    }

    public function canAccess(Model $model){
        
        $company_id = (int) $this->getCompany()->id;

        /**
         * we cannot determine owner...
         */
        if(!isset($model->company_id)){
            return true;
        }

        if($company_id && $model->company_id == $company_id){
            return true;
        }

        return false;

    }

    public function find($id){

        $model = $this->repo->find($id);

        return $this->canAccess($model)? $model: null;
    }

    public function setData($data=null){

        /**
         * was already populated?
         */
        if(!empty($this->data) && empty($data)){
            return;
        }

        if(!app()->runningInConsole()){
            $this->data = array_merge($this->data, app("request")->all() );
            $this->payload();
        }

        if(is_array($data)){
            $this->data = array_merge($this->data, $data);
        }
       

    }

    public function getParam($key, $replacement=null){

        $this->setData();

        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        if(is_array($this->payload) && isset($this->payload[$key])){
            return $this->payload[$key];
        }
        return $replacement;
    }

    public function getParams(){
        
        $this->setData();
        
        return is_array($this->payload) ? array_merge($this->data, $this->payload) : $this->data;
    }


    public function setFilter($obj){

        if(class_exists($obj)){
            $this->filters[] = $obj;
        }else{
            throw new \Exception("Filter $obj class not found");
        }
    }
   
    public function setTransform($obj){

        if(class_exists($obj)){
            $this->transforms[] = $obj;
        }else{
            throw new \Exception("Transformer $obj class not found");
        }
    }

    public function getTransformed(){
        
        $res = $this->get();

        $res = $this->filter($res);

        $res = $this->transform($res);

        return $res->values();
    }

    public function showTransformed($id){

        if(method_exists($this, "show")){
            return $this->transform(
                $this->show($id)
            );
        }

        return $this->transform(
            $this->find($id)
        );
        
    }

    public function getAgg(){

        return $this->aggregates;
    }

    public function filter(Collection $coll){

        if( empty($this->filters) || !is_array($this->filters) ){
            return $coll;
        }

        foreach($this->filters as $filter){
         
            $instance = app()->make($filter);

            if(!method_exists($instance, "filter")){
                throw new \Exception("No filter method on " . $filter);
            }

            $coll = $coll->filter(function($item) use ($instance) {
                return $instance->filter($item);
             });

        }

        return $coll;

    }

    public function transform($coll_or_model){

        if( empty($this->transforms) || !is_array($this->transforms) ){
            return $coll_or_model;
        }

        foreach($this->transforms AS $transformer){

            $instance = app()->make($transformer);

            if(!method_exists($instance, "transform")){
                throw new \Exception("No transform method on " . $transformer);
            }

            if(is_a($coll_or_model, Model::class)){
          
                $coll_or_model = $instance->transform($coll_or_model);
            }

            if($coll_or_model instanceof Collection){
                $coll_or_model->transform(function($item) use($instance) {
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