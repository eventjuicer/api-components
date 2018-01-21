<?php

namespace Eventjuicer\Resources\Restricted;

use Illuminate\Http\Resources\Json\Resource;

use Eventjuicer\Models\Group;

use Eventjuicer\ValueObjects\EmailAddress;


class PerformanceResource extends Resource
{

    protected $visible = ["fname", "lname", "cname2", "position", "phone"];

    protected $skipParentCompany;

    public function __construct($resource, $skipParentCompany = false)
    {
        $this->resource = $resource;
        $this->skipParentCompany = $skipParentCompany;
    }


    public function toArray($request)
    {       

            //dd($this->mergeWith);


            $parentProfile = [];

            $data = [];

            $data["id"] = (int) $this->id;

            $profile = $this->remapFields($this->fields);

            if($this->parent_id)
            {
                $parentProfile = $this->remapFields($this->parent->fields);

            }

            $data["is_subaccount"] = intval($this->parent_id > 0);

            $data["profile"] = isset($parentProfile) ? array_merge($parentProfile, $profile) : $profile;


            $data["parent"] = $this->parent_id ?  new self($this->parent) : [];

            $data["company"] = [];


            if($this->company_id)
            {
                if(!$this->skipParentCompany)
                {
                    $data["company"] = new ApiUserCompanyResource($this->company);

                }
            }
            else
            {
                if($this->parent_id && $this->parent->company_id)
                {
                    $data["company"] = new ApiUserCompanyResource($this->parent->company, true);
                }
            }

            
            $data["email"] = $this->email;

            $data["domain"] = (new EmailAddress($this->email))->domain();


            $data["stats"] = $this->stats;

           

           return $data;
    }


    protected function remapFields($fields)
    {
           return $fields->whereIn("name", $this->visible)->mapWithKeys(function($item)
            {     

                $value = $item->pivot->field_value;

                return [ $item->name => $value ] ;

            })->all();
    }
}



