<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Participant;
// use Carbon\Carbon;
// use Cache;

//use Eventjuicer\Services\Repository;
//use Bosnadev\Repositories\Eloquent\Repository;

use Carbon\Carbon;
use Uuid;

class ParticipantRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Participant::class;
    }

    public function createOrUpdate($active_event_id = 0, $data = array())
    {

        $data = [

            "email"         => $this->request->input("email"),
            "token"         => sha1(Uuid::generate(4)),
            "event_id"      => $active_event_id,
            "group_id"      => 0,
            "organizer_id"  => 0,
            "lang"          => "pl",
            "confirmed"     => 1,
            "createdon"     => Carbon::now()

        ];

        $this->create($data);
    }

    public function toSql()
    {
    	$this->applyCriteria();
    	return $this->model->toSql();
    }




}