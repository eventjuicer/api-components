<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Exhibitors\CompanyData;
 
class PublicPartnerPerformanceResource extends Resource
{

    public function toArray($request)
    {

        $cd = new CompanyData($this->resource);

        $data = [];

        $data["id"] = $this->id;

		$data["company_id"] = $this->company_id;
        $data["name"] = $cd->getName();
        $data["logotype"] = $cd->getLogotypeCdn();
	    $data["stats"] = isset($this->company->stats) ? $this->company->stats : [];

		return $data;


            
    }
}



