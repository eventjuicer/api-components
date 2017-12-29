<?php

namespace Eventjuicer\Repositories;

use Eventjuicer\Models\Meetup;
use Eventjuicer\Repositories\Repository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;

use Carbon\Carbon;
use Uuid;



class MeetupRepository extends Repository
{
    
    protected $preventCriteriaOverwriting = false;

    public function model()
    {
        return Meetup::class;
    }


    public function byCompany($company_id, $orderBy="")
    {

        $this->pushCriteria(new BelongsToCompany($company_id));
     
        $data = $this->with(["participant.fields", "admin.fields"])->all();

        return $data;

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