<?php 

namespace Eventjuicer\Services\Context\ViewComposers;

use Illuminate\Contracts\View\View;

use Eventjuicer\Models\User;

use Eventjuicer\ValueObjects\Amount;


use Contracts\Context;



class Shared {



    private $list_of_groups, 
            $list_of_portals, 
            $list_of_events, 
            $list_of_projects = array();
  

    private $current_event = 0;
    private $current_group = 0;
    private $current_portal = 0;


    private $context;
    private $appcontext;
    private $usercontext;

    public function __construct(Context $context)
    {

        $this->context      = $context->level();
        $this->appcontext   = $context->app();
        $this->usercontext  = $context->user();


        if( ! $this->context->get("organizer_id")  )
        {
            return;
        }



        $this->list_of_projects = $this->context->get_organizer()->groups;
        $this->current_group =  $this->context->get("group_id");

        if( $this->current_group )
        {

        
            if(! $this->appcontext->is("editorapp") )
            {
                $this->list_of_events = $this->context->get_group()->events;
            }


        }
        

        if( $this->context->get("event_id") )
        {
            $this->current_event =  $this->context->get("event_id");
        }




    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {

         $view->with("langs", collect( config("langs") ));


        $view->with("currencies", collect( Amount::getCurrencies() ));

        $view->with('logotype',         $this->list_of_projects  );


        $view->with('list_of_projects', $this->list_of_projects  );
        $view->with('current_project',  $this->current_group  );


        $view->with('list_of_events',   $this->list_of_events );
        $view->with('current_event',    $this->current_event  );


        $view->with("list_of_portals",      collect($this->list_of_projects)->filter(function($group){ return $group->is_portal; }) );
        $view->with("list_of_eventgroups",  collect($this->list_of_projects)->filter(function($group){ return !$group->is_portal; }) );

        $view->with("list_of_users",    $this->context->get_organizer() ? $this->context->get_organizer()->users : []);

        $view->with('account',          $this->context->getParameter("account") );
        $view->with('portal',          $this->context->getParameter("project") );
        $view->with('project',          $this->context->getParameter("project") );

        $view->with('current_user',    User::find( \Auth::id()  )  );




    }

}