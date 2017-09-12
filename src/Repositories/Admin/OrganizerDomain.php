<?php 

namespace Eventjuicer\Repositories\Admin;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Host;

class OrganizerDomain extends Repository
{
	 public function model()
	 {
        return Host::class;
    }



  /*  public function all($columns = array())
    {
    	return $this->findWhere([
    		'type' => "widget"
		]);
    }

    */
}