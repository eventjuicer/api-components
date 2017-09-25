<?php 


namespace Eventjuicer\Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Services\Repository;







use Eventjuicer\Page;




class PageRepository extends Repository
{
   

    protected $preventCriteriaOverwriting = false;



    public function model()
    {
        return Page::class;
    }



   




}