<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Repositories\Repository;

use Eventjuicer\Models\TicketDownload;



class TicketDownloadRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return TicketDownload::class;
    }


   

}