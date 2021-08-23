<?php 

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Repositories\Admin\UserLogRepository;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;
use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Models\UserLog as UserLogModel;
use Illuminate\Support\Facades\Log;

class UserLog {

	protected $request, $repository;

	function __construct(
		Request $request, 
		UserLogRepository $repository
	){

		$this->request = $request;
		$this->repository = $repository;
	}

	public function getByEventId($id){

		$this->repository->pushCriteria(new BelongsToEvent($id));

        return $this->repository->all();

	}

	public function create(Model $model, $user_id, $action=""){

		$backup = array();

		if($model->isDirty()){
			$action = "company.update";
			foreach($model->getDirty() as $column => $newValue ){
				$backup[$column] = $model->{$column};
			}
		}

		$userlog = new UserLogModel;
		$userlog->user_id = $user_id;
		$userlog->organizer_id = 0;
		$userlog->group_id = 0;
		$userlog->event_id = 0;
		$userlog->action = $action;
		$userlog->data = $model->getDirty();
		$userlog->backup = $backup;


		$model->logs()->save($userlog);

	}


}