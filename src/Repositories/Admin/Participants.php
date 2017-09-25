<?php

namespace Eventjuicer\Repositories\Admin;

use Eventjuicer\Services\Repository;

use Eventjuicer\Participant;


use Eventjuicer\ValueObjects\Random;
use Eventjuicer\ValueObjects\EmailAddress;



class Participants extends Repository
{
    
  
    public function model()
    {
        return Participant::class;
    }


    public function recent()
    {
       // return Participant::with("purchase")->where("organizer_id", $this->organizer_id)->orderby('createdon', "DESC")->limit(10)->get();

        return $this->model->with('fields')->where("organizer_id", "=", $this->context->get("organizer_id"))->paginate(15);

    }


    public function paginated()
    {
        return $this->model->with('purchase')->where("organizer_id", "=", $this->context->get("organizer_id"))->where("amount", ">", 0)->orderby("createdon", "DESC")->paginate(50);

    }


    public function firstOrCreate(array $data = [])
    {

        if( !empty($data["token"]))
        {
             $participant = $this->model->where([
                 "event_id"  => $this->context->get("event_id"),
                 "token"     => $data["token"]     
            ])->first();
        }
        else if(!empty($data["email"]))
        {
            $email = new EmailAddress($data["email"]);

            if(!$email->isValid())
            {
                return false;
            }

             $participant = $this->model->where([
                 "event_id"     => $this->context->get("event_id"),
                 "email"        => $email        
            ])->first();
        }

        if($participant)
        {
            return $participant;
        }

        if(empty($data["email"]))
        {
            /*no chance to guess it :) */
            return false;
        }

        $participant        = new Participant;
        $participant->email = (string) new EmailAddress($data["email"]);

        $participant = $this->wrapWithContext( $participant );

        $participant->token = new Random;

        $participant->save();

        return $participant;
    }




}