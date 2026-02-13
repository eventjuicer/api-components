<?php

namespace Eventjuicer\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Eventjuicer\Crud\Traits\UseRequestInfo;
use Eventjuicer\Crud\Traits\UseActiveEvent;
use Eventjuicer\Crud\Traits\UseRouteInfo;


abstract class Crud {

    use UseRequestInfo;
    use UseActiveEvent;
    use UseRouteInfo;

    protected $filters = [];
    protected $transforms = [];
    protected $aggregates = [];


    public function getRepo(){
        return $this->repo;
    }


    public function makeModel(){
        $this->repo->makeModel();
        return $this;
    }

    public function find($id){

        $model = $this->repo->find($id);

        return $this->canAccess($model)? $model: null;
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

    public function getTransformed(...$params){
        
        $res = call_user_func_array(array($this, "get"), $params);

        $res = $this->filter($res);

        $res = $this->transform($res);

        return $res->values();
    }

    public function showTransformed($id){

        if(is_callable(array($this, "show"))){
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