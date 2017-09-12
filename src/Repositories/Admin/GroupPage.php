<?php 

namespace Repositories\Admin;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Page;

class GroupPage extends Repository
{
	 public function model()
	 {
        return Page::class;
    }


    public function all($columns = array())
    {
    	return $this->findWhere([
    		'type' => "page"
		]);
    }

}