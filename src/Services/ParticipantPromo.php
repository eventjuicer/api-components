<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Eventjuicer\Services\Hashids;
use Eventjuicer\Repositories\ParticipantRepository;
use Eventjuicer\Repositories\CreativeRepository;


class ParticipantPromo
{
    
    protected $request, $repo, $creatives;

	protected $participantId, $creativeId = 0;

    protected $participant = [];
	
    protected $promoLink, $participantImage = "";

    function __construct(
    
        Request $request, 

        ParticipantRepository $repo,

        CreativeRepository $creatives


    )
    {

        $this->request = $request;
    	$this->repo = $repo;
        $this->creatives = $creatives;
       


        if((int) $request->route("participant"))
        {
            $this->participantId = (int) $request->route("participant");

            $this->creativeId = (int) $request->route("creative");

        
        }
        else if( is_string( $request->route("hash", false) ) )
        {
            //gateway?

            $this->unHash( $request->route("hash") );
        }

        if(!$this->isValid())
        {
           throw new \Exception("Cannot find user :(");
        }

        $this->build();

    }


    public function isValid()
    {
        return ($this->participantId > 0);
    }


    public function participantId()
    {
        return $this->participantId;
    }

    public function creativeId()
    {
        return $this->creativeId;
    }

    public function participant()
    {
        return $this->participant;
    }

    public function participantName()
    {
        return $this->participantName;
    }

    public function authQrCode()
    {
        return (new Hashids)->encode($this->participantId()) . "@" . substr(array_get($this->participant(), "token"), 0, 5);

    }

    public function participantImage()
    {
        return $this->participantImage;
    }

    public function field($field)
    {
        return array_get($this->participant, "fields.".(string) $field, "");
    }

    protected function build()
    {

        $this->participant = $this->repo->toSearchArray($this->participantId);

        $this->participantName =  array_get($this->participant, "fields.cname2",
                                    array_get($this->participant, "fields.cname")
                                   );


        // $this->promoLink = url("/?utm_source=partner_".$this->participantId."&utm_medium=".str_slug($this->participantName)."&utm_campaign=visitors_TEH13");


        $this->participantImage = array_get($this->participant, "fields.logotype", 
                                    array_get($this->participant, "fields.avatar")
                                );
    }
    

    protected function unHash(string $hash)
    {
        $this->creativeId = (new Hashids(true))->decode($hash);

        if($this->creativeId > 0)
        {
            $creative = $this->creatives->find($this->creativeId);

            $this->participantId = !is_null($creative) ? $creative->participant_id : 0;
        }

    }


}