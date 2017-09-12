<?php 

namespace Eventjuicer\Repositories\Admin;
//use Bosnadev\Repositories\Eloquent\Repository;
use Eventjuicer\Repository;
use Eventjuicer\Models\Topic;

class TopicRepository extends Repository
{
   

    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Topic::class;
    }
 

    


}