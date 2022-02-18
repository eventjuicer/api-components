<?php

namespace Eventjuicer\Requests\Posts;

use Illuminate\Http\Request;

class RestrictedPostRequest extends Request {

        
        // protected $requiredParams = [
        //         "company_id"
        // ];

        public function withValation(){


                $request->merge(["company_id" => $company->id]);


        }

        public function getData(){
                return app("request")->all();
        }

        protected function prepareForValidation()
        {

        dd("asd");
        $this->merge([
                'slug' => Str::slug($this->slug),
        ]);
        }
        
        public function rules(){
                return [
                        'title' => 'required|unique:posts|max:255',
                        'body' => 'required',
                ];
        }
   
        public function authorize(){

                return false;
        }

}       
