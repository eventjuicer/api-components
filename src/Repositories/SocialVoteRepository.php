<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\SocialVote;
// use Carbon\Carbon;
// use Cache;




class SocialVoteRepository extends Repository {
    

    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return SocialVote::class;
    }



   


}