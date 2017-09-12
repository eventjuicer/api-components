<?php 

namespace Repositories\Admin;
//use Bosnadev\Repositories\Eloquent\Repository;
use Services\Repository;
use Eventjuicer\Topic;

class TopicRepository extends Repository
{
   

    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Topic::class;
    }
 

    


}