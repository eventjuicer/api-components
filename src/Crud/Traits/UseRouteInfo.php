<?php

namespace Eventjuicer\Crud\Traits;

use Eventjuicer\Services\Resolver;

trait UseRouteInfo {

    protected $routeParams = [];
    protected $host = "";
    protected $resolver_instances = [];

    public function routeParams(){
        $routeInfo = app("request")->route();
        if(!empty($routeInfo[2])){
            $this->routeParams = $routeInfo[2];
        }
    }

    public function getRouteParam($key){

        if(empty($this->routeParams)){
            $this->routeParams();
        }

        return isset($this->routeParams[$key])? $this->routeParams[$key]: null;
    }

    public function getContextFromHost(){

        $host = $this->getRouteParam("host");

        if(!isset($this->resolver_instances[$host])){
           $this->resolver_instances[$host] = new Resolver($host);
        }

        return $this->resolver_instances[$host];

       if(!app()->runningInConsole()){

       }
    }



}