<?php

namespace Eventjuicer\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use Eventjuicer\Services\Hashids;


use Eventjuicer\Repositories\CreativeRepository;
use Eventjuicer\Repositories\CreativeTemplateRepository;

use Eventjuicer\Repositories\Criteria\BelongsToParticipant;
use Eventjuicer\Repositories\Criteria\BelongsToEvent;

use Eventjuicer\Services\ImageAddText;
use Eventjuicer\Services\ImageEncode;


use Illuminate\Database\Eloquent\Model;



use Storage;

class ParticipantPromoCreatives
{

    protected $promo, $creatives, $templates;

    protected $all;

    protected $act_as = ["newsletter", "social", "banner", "link"];

    function __construct(
        ParticipantPromo $promo,
        CreativeRepository $creatives, 
        CreativeTemplateRepository $templates
    )
    {
        $this->promo = $promo;
        $this->creatives = $creatives;   
        $this->templates = $templates;

        $this->loadCreatives(); 

    }


    public function getPromo()
    {
        return $this->promo;
    }

    /*
    *   $creativeId is primary Id from DB!
    */

    public function buildLink($creativeId, $target = "")
    {

        $url = url("~" . $this->buildHash($creativeId));

        $encUrl = rawurlencode($url);

        switch($target)
        {
            case "linkedin":

            return "https://www.linkedin.com/shareArticle?mini=true&url=".$encUrl."&title=title&summary=summary&source=source";
            break;

            case "twitter":
                return "https://twitter.com/home?status=" . $encUrl;
            break;

            case "facebook":
                return "https://www.facebook.com/sharer/sharer.php?u=" . $encUrl;
            break;
        }

        return $url;
    }


    public function current()
    {
        return $this->creatives->find( $this->promo->creativeId() );
    }

    public function openGraph()
    {
        $data = [];

        $creative = $this->current();

        $data["title"] = array_get($creative->data, "title");
        $data["description"] = array_get($creative->data, "description");
        $data["image"] =  $this->buildPublicFilename($creative->id);

        if($creative->act_as != "social")
        {
            $data["image"] =  $this->defaultImage($creative->id);
            $data["title"] =  sprintf(config("promo.og_title"), $this->promo->field("booth"));
            $data["description"] = sprintf(config("promo.og_description"), $this->promo->participantName());
        }

        return $data;
       
        
    }

    public function targetUrl()
    {
        $creative = $this->current();

        return sprintf(config("promo.link"), 

                    $creative->participant_id, 
                    $creative->act_as, 
                    $creative->id
                );
    }

    public function defaultImage($creative)
    {
        if(!$this->promo->participantImage())
        {
            return "";
        }

        $target = $this->buildLocalFilename($creative);

        if(!file_exists($target))
        {
            (new ImageEncode( $this->promo->participantImage(), $target))->save();
        }

        return $this->buildPublicFilename($creative);

    }

    public function autogenerateIfNone(string $act_as)
    {

        $items = $this->filtered($act_as);

        if(!$items->count())
        {
            $this->autogenerate($act_as);
        }

    }

    public function autogenerate(string $act_as)
    {

        $data = [

            "name" => $act_as . " #1",
            "template_id" => 0,
            "data" => [],
            "act_as" => $act_as
        ];
        
        $this->save($data);

    }


    public function buildHash($creativeId)
    {

        if($creativeId instanceOf Model)
        {
            $creativeId = $creativeId->id;
        }

        return (new Hashids(true))->encode($creativeId);
    }


    public function buildLocalFilename($creativeId, $ext = "jpg")
    {
        return storage_path("app/public/" . $this->buildFilename($creativeId, $ext));
    }  

    public function buildPublicFilename($creativeId, $ext = "jpg")
    {
        return asset("storage/" . $this->buildFilename($creativeId, $ext));
    }   

    public function buildFilename($creativeId, $ext = "jpg")
    {
        return $this->buildHash($creativeId) . "." . trim($ext,".");
    }  


    public function templates()
    {

        return $this->loadTemplates();
    }


    public function filtered($act_as)
    {
        return $this->all->filter(function($v, $k) use ($act_as)
        {
            return $v->act_as == $act_as;
        });
    }

    public function save(array $data)
    {
        
        $data["participant_id"] = $this->promo->participantId();
        
        $data["organizer_id"] = array_get($this->promo->participant(), "organizer_id");
        $data["group_id"] = array_get($this->promo->participant(), "group_id");
        $data["event_id"] = array_get($this->promo->participant(), "event_id");
        
        //enum('newsletter', 'social', 'banner', '')    

        $data["act_as"] = !empty($data["act_as"]) ? $data["act_as"] : "social";
         
        $creative = $this->creatives->create($data);

        //reload!

        $this->loadCreatives(); 

        return $creative;
    }

    protected function validateActAs()
    {

    }


    protected function loadTemplates()
    {
       $event_id = array_get($this->promo->participant(), "event_id", 0);

        $this->templates->pushCriteria(
            new BelongsToEvent($event_id)
        );

        return $this->templates->all();
    }


    protected function loadCreatives()
    {
        $this->creatives->pushCriteria(
            new BelongsToParticipant($this->promo->participantId() )
        );

        $this->all = $this->creatives->all();
    }
 


}
