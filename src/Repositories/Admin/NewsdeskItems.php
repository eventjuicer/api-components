<?php 

namespace Eventjuicer\Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;

use Eventjuicer\NewsdeskItem;

class NewsdeskItems extends Repository
{
   

    protected $preventCriteriaOverwriting = false;


    public function model()
    {
        return NewsdeskItem::class;
    }
 



}