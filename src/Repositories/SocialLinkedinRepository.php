<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\SocialLinkedin;
// use Carbon\Carbon;
// use Cache;




class SocialLinkedinRepository extends Repository {
    

    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return SocialLinkedin::class;
    }



   


}