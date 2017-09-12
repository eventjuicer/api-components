<?php

namespace Eventjuicer\Repositories\Admin;

use Eventjuicer\Models\Purchase;

use Carbon;

use Cache;

use Contracts\Context;

class OrganizerPurchases
{
    
    private $organizer_id;

    protected $context;


    function __construct(Context $context)
    {
        $this->context = $context;

        $this->organizer_id = $this->context->level()->get("organizer_id");
    }


    public function recent()
    {
        return Purchase::with("participant")->where("organizer_id", $this->organizer_id)->orderby('createdon', "DESC")->limit(10)->get();

    }


    public function paginated()
    {
        return Purchase::with('participant')->where("organizer_id", "=", $this->organizer_id)->where("amount", ">", 0)->orderby("createdon", "DESC")->paginate(50);

    }


    public function by_id($id = 0)
    {
        return Purchase::with("participant")->findOrFail($id);
    }

}