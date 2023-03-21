<?php 

namespace Eventjuicer\Crud\CompanyPeople;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\CompanyPeopleRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Illuminate\Support\Collection;
use Eventjuicer\ValueObjects\EmailAddress;


class FetchCompanyPerson extends Crud {

   
    protected $repo;
 
    function __construct(CompanyPeopleRepository $repo){
        $this->repo = $repo;
    }

    public function getForParticipant(Participant $participant, string $role=""): Collection{

        if(!$participant->company_id){
            throw new \Exception("No company assigned");
        }

        if($role && stristr($role, "_manager")===false){
            $role = $role. "_manager";
        }

        $this->repo->pushCriteria(new BelongsToCompany($participant->company_id));
        $this->repo->pushCriteria(new FlagEquals("disabled", 0));
        if($role){
            $this->repo->pushCriteria(new FlagEquals("role", $role));
        }
 
        $res = $this->repo->all();

        //remap for Mailable

        return $res->map(function($item){
            $item->name = $item->fname . " " . $item->lname;
            return $item;
        });

    }

    public function getForParticipantFiltered($participant, $role=""): Collection{

        $coll = $this->getForParticipant($participant, $role);

        //filter for Mailable

        return $coll->filter(function($item){
            return (new EmailAddress($item->email))->isValid();
        });
    }

}