<?php

namespace Eventjuicer\Crud\TicketDownloads;

use Eventjuicer\Models\TicketDownload;
use Eventjuicer\Repositories\TicketDownloadRepository;
use Eventjuicer\Crud\Traits\Aggregate;

class AggregateResponse {

    use Aggregate;

    public $repo;

    function __construct(TicketDownloadRepository $repo){
        $this->repo = $repo;
    }

    public function transform(TicketDownload $item){

        
        if($item->going){
            $this->increment("going");
        }

        if(!$item->going){
            $this->increment("not_going");
        }

        
        return $item;
        
    }


}