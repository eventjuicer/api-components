<?php

namespace Eventjuicer\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Eventjuicer\Services\Exhibitors\CompanyData;
use Eventjuicer\ValueObjects\CloudinaryImage;
use Eventjuicer\Services\Exhibitors\Creatives;

class PublicPartnerPerformanceResource extends Resource
{

    public function toArray($request)
    {

        $cd = new CompanyData($this->resource);

        $logotype = $cd->getLogotypeCdn();

        $data = [];

        $data["id"] = $this->id;

		$data["company_id"] = $this->company_id;
        $data["name"] = $cd->getName() ? $cd->getName() : $this->company->slug;
        $data["slug"] = $this->company->slug;
        $data["logotype"] = $logotype ? (new CloudinaryImage($logotype))->thumb() : "";
	    $data["stats"] = isset($this->company->stats) ? $this->company->stats : [];
        $data["creatives"] = (new Creatives($cd))->get();

		return $data;


            
    }
}



