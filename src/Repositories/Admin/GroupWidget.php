<?php 

namespace Eventjuicer\Repositories\Admin;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;

use Eventjuicer\Widget;

class GroupWidget extends Repository
{
	 public function model()
	 {
        return Widget::class;
    }



    public function all($columns = array())
    {
    	return $this->findWhere([
    		'type' => "widget"
		]);
    }

}