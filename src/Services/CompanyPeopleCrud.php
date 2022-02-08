<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Validator;
use Eventjuicer\Repositories\CompanyPeopleRepository;

class CompanyPeopleCrud {

    protected $request, $data, $repo, $id;

    function __construct(Request $request, CompanyPeopleRepository $repo){
        $this->request = $request;
        $this->data = json_decode($request->getContent(), true);
        $this->repo = $repo;
    }

    function validate(){
        
        $rules = [
            'fname' => 'required|min:2|max:200', 
            'lname' => 'required|min:2|max:200',
            'email' => 'required|email',
            'role' => 'required|alpha|min:10',
            'phone' => 'required|numeric|min:9|max:20',
            'company_id' => 'required|numeric'
        ];

        $validator = Validator::make($this->data, $rules);
        
        if ($validator->passes()) {
            return true;
        } else {
            //TODO Handle your error
            return $validator->errors()->all();
        }
    }

    function find($id){
        return $this->repo->find($id);
    }

    function create(){

        $id = $this->repo->saveModel($this->data);

        return$this->find($id);
    }

    function update($id){
        
    }

    function delete($id){

    }


    function __set($name, $value){
        $this->data[$name] = $value;
    }
    
}