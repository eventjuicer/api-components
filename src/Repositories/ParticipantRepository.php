<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\Repository;

use Carbon\Carbon;
use Uuid;

use Eventjuicer\Resources\ParticipantResource;


class ParticipantRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Participant::class;
    }


    public function findApiUserByEmail(string $emailAddress)
    {
        $this->pushCriteria(new Criteria\ColumnGreaterThanZero("company_id"));
        $this->pushCriteria(new Criteria\ColumnMatches("email", $emailAddress));

        return $this->all();
    }


    public function byToken($token)
    {

        $data = $this->with(["fields", "purchases.tickets.flags"])->findBy("token", $token);

        if(is_null($data))
        {
            return [];
        }

        return (new ParticipantResource( $data ))->toArray($this->request );
    }



    public function toSearchArray($id, $columns = [])
    {

        $data = $this->with(["fields", "purchases.tickets.flags"])->find($id);

        if(is_null($data))
        {
            return [];
        }

        return (new ParticipantResource( $data ))->toArray($this->request );
    }

    public function profile($key = "", $replacement = "")
    {

        $profile = $this->fields->mapWithKeys(function($_item){
                
                return [$_item->name => $_item->pivot->field_value];
        });
        return !empty($key) ? $profile->get($key, $replacement) : $profile->all();    
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