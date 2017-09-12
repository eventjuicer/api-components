<?php 

namespace Eventjuicer\Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;

use Eventjuicer\NewsdeskSource;

class NewsdeskSources extends Repository
{
   

    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return NewsdeskSource::class;
    }
 



}