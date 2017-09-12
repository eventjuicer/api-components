<?php 


namespace Repositories\Admin;

//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;







use Eventjuicer\Page;




class PageRepository extends Repository
{
   

    protected $preventCriteriaOverwriting = false;



    public function model()
    {
        return Page::class;
    }



   




}