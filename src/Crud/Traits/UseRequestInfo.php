<?php

namespace Eventjuicer\Crud\Traits;

use Illuminate\Database\Eloquent\Model;
use Validator;


trait UseRequestInfo {

    protected $data = [];
    protected $payload = [];
    protected $errors;


    public function isValid(array $rules = []){
        $validator = Validator::make($this->getParams(), $rules);
        $this->errors = $validator->errors();
        return $validator->passes();
    }

    public function errors(){
        $errorsToArray = $this->errors->toArray();
        return !empty($this->errors)? array_keys( $errorsToArray ): [];
    }

    
    final public function payload(){
        $this->payload = json_decode(app("request")->getContent(), true);
    }
    
    final public function getUser(){
        return app("request")->user();
    }

    final public function getCompany(){
        return app("request")->user()? app("request")->user()->company : null;
    }

    final public function getCompanyId(){
        $company = $this->getCompany();
        return $company? $company->id : 0;
    }

    final public function getGroupId(){
        return $this->getUser()->group_id;
    }

    final public function getCompanyParticipants(){
        return $this->getCompany()->participants->pluck("id")->all();
    }

    final public function canAccess(Model $model){
        
        /**
         * we cannot determine owner...
         */
        if(!isset($model->company_id) || !app("request")->user() ){
            return true;
        }

        if($model->company_id == $this->getCompanyId() ){
            return true;
        }

        return false;

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

    public function setParam($key, $value){
        $this->data[$key] = $value;
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

}