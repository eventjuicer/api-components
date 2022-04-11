<?php 

namespace Eventjuicer\Services\Vipcodes;

use Eventjuicer\Models\CompanyVipcode;
use Carbon\Carbon;

class ShouldBeExpired {

    protected $vipcode;

    function __construct(CompanyVipcode $vipcode){
        $this->vipcode = $vipcode;
    }

    public function check(){
        return $this->vipcode->participant_id || $this->vipcode->email &&  Carbon::now()->gt( $this->blockedTill() );
    }

    public function blockedTill(){
        return $this->vipcode->updated_at->addDay();
    }
}