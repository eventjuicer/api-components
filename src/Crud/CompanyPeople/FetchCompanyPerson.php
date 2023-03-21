<?php 

namespace Eventjuicer\Crud\CompanyPeople;

use Eventjuicer\Crud\Crud;
use Eventjuicer\Models\Participant;
use Eventjuicer\Repositories\CompanyPeopleRepository;
use Eventjuicer\Repositories\Criteria\BelongsToCompany;
use Eventjuicer\Repositories\Criteria\FlagEquals;
use Eventjuicer\Repositories\Criteria\WhereIn;
use Illuminate\Support\Collection;
use Eventjuicer\ValueObjects\EmailAddress;


class FetchCompanyPerson extends Crud {

   
    protected $repo;
    protected $allowed_roles = ["event_manager", "sales_manager", "pr_manager"];


    function __construct(CompanyPeopleRepository $repo){
        $this->repo = $repo;
    }

    public function getForParticipant(Participant $participant, $roles): Collection{

        if(!$participant->company_id){
            throw new \Exception("No company assigned");
        }

        $roles = $this->handleRolesInput($roles);

        $this->repo->pushCriteria(new BelongsToCompany($participant->company_id));
        $this->repo->pushCriteria(new FlagEquals("disabled", 0));
        if($roles->count()){
            $this->repo->pushCriteria(new WhereIn("role", $roles->all() ));
        }
 
        $res = $this->repo->all();

        //remap for Mailable

        $res->transform(function($item){
            $item->name = $item->fname . " " . $item->lname;
            return $item;
        });

        return $res;
    }

    public function getForParticipantFiltered($participant, $roles): Collection{

        $coll = $this->getForParticipant($participant, $roles);

        //filter for Mailable

        $coll = $coll->filter(function($item){
            return (new EmailAddress($item->email))->isValid();
        });

        return $coll;
    }

    private function handleRolesInput($roles): Collection {

        if(is_string($roles)){
            $roles = explode(",", $roles);
        }

        $roles = collect($roles);

        if($roles->first() === "all"){
            $roles = collect($this->allowed_roles);
        }else{

            $roles->transform(function($role){
                return $this->normalizeRole($role);
            });
        }

        return $roles;
    }

    private function normalizeRole(string $role){

        $role = strtolower(trim($role));

        if(in_array($role, $this->allowed_roles)){
            return $role;
        }else{
            return $role . "_manager";
        }

    }

}